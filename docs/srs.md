# SRS: MarginAI — HPP & AI Pricing for UMKM

Web app for UMKM owners: compute HPP (cost of goods manufactured) from materials + direct labor + overhead, then recommend selling price (markup/margin formula + AI advice on psychological pricing, competition, margin risk warning).

Sectors (`businesses.category` enum): `kuliner` (F&B, katering, bakeri), `konveksi` (jahit, sablon, garmen), `kerajinan` (suvenir, furnitur, perhiasan), `rumahan` (sabun, kosmetik, kimia RT).

## Stack
- Actual install (wins over SRS draft): Laravel 13, PHP 8.4, Pest 5. SRS draft said Laravel 12 / PHP 8.3.
- MySQL 8; Blade/Livewire + Tailwind; AI via Laravel AI SDK or Prism; reports via spatie/laravel-pdf + maatwebsite/excel (not installed yet; adding deps needs approval).

## Roles
- owner (primary): manage business profile + default margin, master data (materials/labor/overhead), BOM, run HPP + AI pricing, what-if sim, dashboard, PDF/Excel export.
- staff (optional): log actual material usage per day/batch; view products + final price. No access to global cost config or AI prompts.

## Functional requirements
- FR-01 Auth & profile: register, email verify, login, password reset; business profile with category + default margin %.
- FR-02 Raw materials CRUD: unit (gram, kg, meter, pcs, liter), unit price, wastage %.
- FR-03 Labor: direct labor rate per hour / per day / per unit or batch.
- FR-04 Overhead: fixed & variable (listrik, gas, air, sewa, kemasan, penyusutan alat); proportional allocation to batches.
- FR-05 BOM: product = materials × qty per batch + yield + labor + overhead.
- FR-06 HPP engine: real-time HPP per batch and per unit (formulas below).
- FR-07 AI price recommendation: base price from formula; AI reads business category for psychological pricing, margin risk, competition.
- FR-08 What-if sim: revenue, cost, net profit, BEP under price/volume/material-price changes.
- FR-09 Dashboard (margin charts, cost KPIs) + PDF/Excel HPP report.

## Formulas
- material_total = Σ(qty_used × unit_price × (1 + wastage%/100))
- batch_cost = material_total + labor_cost + overhead_allocated
- hpp_unit = batch_cost / batch_yield
- price_markup = hpp_unit × (1 + markup%/100)
- price_margin = hpp_unit / (1 − margin%/100)
- variable_hpp_unit = (material_cost + direct_labor_cost + variable_overhead) / batch_yield
- projected_revenue = simulated_price × estimated_units_sold
- projected_profit = projected_revenue − (variable_hpp_unit × estimated_units_sold) − fixed_overhead_period
- bep_units = ceil(fixed_overhead_period / (price_unit − variable_hpp_unit))
- Cost classification for simulation/BEP: raw materials, direct labor, and variable overhead are variable. Direct labor is variable because labor is recorded as a quantity consumed per product batch, including hourly/daily rates. Fixed overhead remains fixed and is allocated to a product using its existing per-batch allocation × batches per month. If price is not greater than variable HPP, BEP is undefined (`null`). Fixed payroll independent of output is not modeled as direct labor; record it as fixed overhead.

## Non-functional
- Perf: HPP calc & sim updates < 500 ms. AI calls async (Queue job or Livewire loading state). Cache master data.
- Security: tenant isolation by `business_id` (global scope); Form Request validation on every input; CSRF, XSS escaping, bcrypt/argon2id.
- UX: mobile-first responsive; Rupiah format `Rp 1.234.567,00`; margin badge green/yellow/red.
- Money: decimal(15,4) for intermediate values, decimal(15,2) for final values. Never float.

## Schema (initial)
All tables have id bigint PK + timestamps. Tenant-owned tables carry business_id FK.
- users: name, email unique, password
- businesses: user_id FK, name, category enum(kuliner,konveksi,kerajinan,rumahan), default_margin_percent dec(5,2)
- raw_materials: business_id FK, name, unit varchar(50), unit_price dec(15,4), wastage_percent dec(5,2) 0–100
- products: business_id FK, name, sku varchar(100) null, batch_yield dec(10,2), labor_cost_per_batch dec(15,2), overhead_cost_per_batch dec(15,2), calculated_hpp dec(15,2) (per unit), recommended_price dec(15,2), selling_price dec(15,2) (owner-set)
- product_materials (BOM pivot): product_id FK, raw_material_id FK, quantity_used dec(12,4), subtotal_cost dec(15,2)
- price_simulations: product_id FK, simulated_price dec(15,2), estimated_units_sold int, projected_revenue dec(15,2), projected_profit dec(15,2), material_price_changes json null, variable_material_cost_per_unit dec(15,4) null, variable_labor_cost_per_unit dec(15,4) null, variable_overhead_cost_per_unit dec(15,4) null, variable_cost_per_unit dec(15,4) null, fixed_cost_per_period dec(15,2) null, break_even_units bigint null, ai_notes text null

## Roadmap
1. Setup: migrations, auth (Breeze/Fortify), tenant middleware/scope, user & business profile.
2. Master data: raw materials (wastage), labor, overhead, interactive BOM.
3. HPP engine as Service/Action class; what-if sim, net profit, BEP; Pest tests for formulas.
4. AI: SDK/Prism integration; structured prompt with HPP, category, target margin → competitive price, psychological pricing, margin risk; queue + cache.
5. Dashboard charts (HPP vs price, profit), PDF/Excel export, E2E + mobile testing, release.
