const demoTrendValues = [
    { hpp: '12100.00', price: '18000.00', profit: '240000.00' },
    { hpp: '12500.00', price: '18500.00', profit: '310000.00' },
    { hpp: '12300.00', price: '18200.00', profit: '-15000.00' },
    { hpp: '12450.00', price: '19000.00', profit: '350000.00' },
];
const demoTrendStart = new Date();
demoTrendStart.setUTCDate(1);
demoTrendStart.setUTCMonth(demoTrendStart.getUTCMonth() - 11);
const demoTrends = Array.from({ length: 12 }, (_, index) => {
    const month = new Date(demoTrendStart);
    month.setUTCMonth(demoTrendStart.getUTCMonth() + index);
    const values = demoTrendValues[index - 8] ?? null;

    return {
        month: month.toISOString().slice(0, 7),
        average_hpp: values?.hpp ?? null,
        average_simulated_price: values?.price ?? null,
        average_projected_profit: values?.profit ?? null,
        simulation_count: values ? 1 : 0,
    };
});

const demoDashboard = {
    business: {
        name: 'Kedai Pagi',
        category: 'Kuliner',
    },
    kpis: {
        product_count: 4,
        average_hpp: '12450.00',
        average_recommended_price: '18600.00',
        average_selling_price: '17800.00',
        average_margin_percent: '29.32',
        average_material_cost_per_batch: '5200.00',
        average_labor_cost_per_batch: '1800.00',
        average_overhead_cost_per_batch: '1250.00',
    },
    trends: demoTrends,
    products: [
        {
            id: 'demo-1',
            name: 'Roti Kopi',
            sku: 'ROT-001',
            calculated_hpp: '4800.00',
            recommended_price: '7500.00',
            selling_price: '7000.00',
            materials: [
                { id: 'demo-flour', name: 'Tepung terigu', unit: 'kg', unit_price: '12000.0000' },
                { id: 'demo-coffee', name: 'Kopi bubuk', unit: 'kg', unit_price: '85000.0000' },
            ],
        },
        {
            id: 'demo-2',
            name: 'Cookies Cokelat',
            sku: 'CKS-002',
            calculated_hpp: '18000.00',
            recommended_price: '25000.00',
            selling_price: null,
            materials: [
                { id: 'demo-chocolate', name: 'Cokelat batang', unit: 'kg', unit_price: '64000.0000' },
                { id: 'demo-butter', name: 'Mentega', unit: 'kg', unit_price: '42000.0000' },
            ],
        },
        {
            id: 'demo-3',
            name: 'Es Kopi Susu',
            sku: 'KOP-003',
            calculated_hpp: '6500.00',
            recommended_price: '9500.00',
            selling_price: '9500.00',
            materials: [
                { id: 'demo-milk', name: 'Susu segar', unit: 'liter', unit_price: '18000.0000' },
                { id: 'demo-coffee-beans', name: 'Kopi bubuk', unit: 'kg', unit_price: '85000.0000' },
            ],
        },
        {
            id: 'demo-4',
            name: 'Paket Snack',
            sku: 'SNK-004',
            calculated_hpp: '16500.00',
            recommended_price: '19000.00',
            selling_price: '19000.00',
            materials: [
                { id: 'demo-snack', name: 'Aneka snack', unit: 'pcs', unit_price: '15000.0000' },
                { id: 'demo-packaging', name: 'Kemasan', unit: 'pcs', unit_price: '2500.0000' },
            ],
        },
    ],
};

const elements = {
    businessName: document.querySelector('#business-name'),
    businessCategory: document.querySelector('#business-category'),
    modeBadge: document.querySelector('#mode-badge'),
    loginButton: document.querySelector('#login-open'),
    logoutButton: document.querySelector('#logout-button'),
    exportLinks: document.querySelector('#export-links'),
    alert: document.querySelector('#app-alert'),
    loginDialog: document.querySelector('#login-dialog'),
    loginForm: document.querySelector('#login-form'),
    loginError: document.querySelector('#login-error'),
    loginSubmit: document.querySelector('#login-submit'),
    search: document.querySelector('#product-search'),
    products: document.querySelector('#product-list'),
    productAdd: document.querySelector('#product-add'),
    productDialog: document.querySelector('#product-dialog'),
    productDialogTitle: document.querySelector('#product-dialog-title'),
    productDialogClose: document.querySelector('#product-dialog-close'),
    productDialogCancel: document.querySelector('#product-dialog-cancel'),
    productForm: document.querySelector('#product-form'),
    productSubmit: document.querySelector('#product-submit'),
    productFormError: document.querySelector('#product-form-error'),
    productMaterialOptions: document.querySelector('#product-material-options'),
    productLaborOptions: document.querySelector('#product-labor-options'),
    productOverheadOptions: document.querySelector('#product-overhead-options'),
    productMaterialTotal: document.querySelector('#product-material-total'),
    productLaborTotal: document.querySelector('#product-labor-total'),
    productOverheadTotal: document.querySelector('#product-overhead-total'),
    productBatchTotal: document.querySelector('#product-batch-total'),
    productHppUnit: document.querySelector('#product-hpp-unit'),
    marginSummary: document.querySelector('#margin-summary'),
    accessPanel: document.querySelector('#access-panel'),
    verifyButton: document.querySelector('#verify-button'),
    setupPanel: document.querySelector('#setup-panel'),
    businessForm: document.querySelector('#business-form'),
    pricingTargetLabel: document.querySelector('#pricing-target-label'),
    pricingTargetHelp: document.querySelector('#pricing-target-help'),
    dashboardContent: document.querySelector('#dashboard-content'),
    dashboardTrends: document.querySelector('#dashboard-trends'),
    simulationForm: document.querySelector('#price-simulation-form'),
    simulationProduct: document.querySelector('#simulation-product'),
    simulationMaterialChanges: document.querySelector('#simulation-material-changes'),
    simulationSubmit: document.querySelector('#simulation-submit'),
    simulationFormError: document.querySelector('#simulation-form-error'),
    simulationAccessNote: document.querySelector('#simulation-access-note'),
    simulationResult: document.querySelector('#simulation-result'),
    simulationResultTitle: document.querySelector('#simulation-result-title'),
    simulationResultStatus: document.querySelector('#simulation-result-status'),
    simulationResultRevenue: document.querySelector('#simulation-result-revenue'),
    simulationResultCost: document.querySelector('#simulation-result-cost'),
    simulationResultProfit: document.querySelector('#simulation-result-profit'),
    simulationResultBep: document.querySelector('#simulation-result-bep'),
    simulationResultCostDetail: document.querySelector('#simulation-result-cost-detail'),
    simulationResultRecommendation: document.querySelector('#simulation-result-recommendation'),
    simulationHistoryCount: document.querySelector('#simulation-history-count'),
    simulationHistoryError: document.querySelector('#simulation-history-error'),
    simulationHistoryList: document.querySelector('#simulation-history-list'),
    rawMaterialSection: document.querySelector('#raw-material-section'),
    laborCostSection: document.querySelector('#labor-cost-section'),
    overheadCostSection: document.querySelector('#overhead-cost-section'),
    rawMaterialList: document.querySelector('#raw-material-list'),
    rawMaterialAdd: document.querySelector('#raw-material-add'),
    rawMaterialDialog: document.querySelector('#raw-material-dialog'),
    rawMaterialDialogTitle: document.querySelector('#raw-material-dialog-title'),
    rawMaterialDialogClose: document.querySelector('#raw-material-dialog-close'),
    rawMaterialDialogCancel: document.querySelector('#raw-material-dialog-cancel'),
    rawMaterialForm: document.querySelector('#raw-material-form'),
    rawMaterialSubmit: document.querySelector('#raw-material-submit'),
    rawMaterialFormError: document.querySelector('#raw-material-form-error'),
    laborCostList: document.querySelector('#labor-cost-list'),
    laborCostAdd: document.querySelector('#labor-cost-add'),
    laborCostDialog: document.querySelector('#labor-cost-dialog'),
    laborCostDialogTitle: document.querySelector('#labor-cost-dialog-title'),
    laborCostDialogClose: document.querySelector('#labor-cost-dialog-close'),
    laborCostDialogCancel: document.querySelector('#labor-cost-dialog-cancel'),
    laborCostForm: document.querySelector('#labor-cost-form'),
    laborCostSubmit: document.querySelector('#labor-cost-submit'),
    laborCostFormError: document.querySelector('#labor-cost-form-error'),
    laborCostQuantityHelp: document.querySelector('#labor-cost-quantity-help'),
    overheadCostList: document.querySelector('#overhead-cost-list'),
    overheadCostAdd: document.querySelector('#overhead-cost-add'),
    overheadCostDialog: document.querySelector('#overhead-cost-dialog'),
    overheadCostDialogTitle: document.querySelector('#overhead-cost-dialog-title'),
    overheadCostDialogClose: document.querySelector('#overhead-cost-dialog-close'),
    overheadCostDialogCancel: document.querySelector('#overhead-cost-dialog-cancel'),
    overheadCostForm: document.querySelector('#overhead-cost-form'),
    overheadCostAmountLabel: document.querySelector('#overhead-cost-amount-label'),
    overheadCostAllocationHelp: document.querySelector('#overhead-cost-allocation-help'),
    overheadCostSubmit: document.querySelector('#overhead-cost-submit'),
    overheadCostFormError: document.querySelector('#overhead-cost-form-error'),
};

let currentProducts = [];
let currentUser = null;
let currentRawMaterials = [];
let currentLaborCosts = [];
let currentOverheadCosts = [];
let editingProduct = null;
let currentSimulations = [];
let simulationPollTimer = null;
let simulationRequestVersion = 0;

function formatRupiah(value) {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    const match = String(value).trim().match(/^(-?)(\d+)(?:\.(\d+))?$/);

    if (!match) {
        return '—';
    }

    const isNegative = match[1] === '-';
    let whole = BigInt(match[2]);
    const decimals = (match[3] ?? '').padEnd(3, '0');
    let cents = BigInt(decimals.slice(0, 2));

    if (Number(decimals[2]) >= 5) {
        cents += 1n;
    }

    if (cents === 100n) {
        whole += 1n;
        cents = 0n;
    }

    const groupedWhole = whole.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    const formattedCents = cents.toString().padStart(2, '0');
    const sign = isNegative && (whole > 0n || cents > 0n) ? '-' : '';

    return `${sign}Rp ${groupedWhole},${formattedCents}`;
}

function toMinorUnits(value) {
    if (value === null || value === undefined || value === '') {
        return null;
    }

    const match = String(value).trim().match(/^(-?)(\d+)(?:\.(\d+))?$/);

    if (!match) {
        return null;
    }

    let units = BigInt(match[2]) * 100n;
    const decimals = (match[3] ?? '').padEnd(3, '0');
    let cents = BigInt(decimals.slice(0, 2));

    if (Number(decimals[2]) >= 5) {
        cents += 1n;
    }

    if (cents === 100n) {
        units += 100n;
        cents = 0n;
    }

    units += cents;

    return match[1] === '-' ? -units : units;
}

function decimalToScaledUnits(value, precision) {
    const match = String(value ?? '').trim().match(/^(-?)(\d+)(?:\.(\d+))?$/);

    if (!match) {
        return null;
    }

    const decimals = match[3] ?? '';
    const retainedDecimals = decimals.slice(0, precision).padEnd(precision, '0');
    let units = BigInt(match[2]) * (10n ** BigInt(precision))
        + BigInt(retainedDecimals || '0');

    if (decimals.length > precision && Number(decimals[precision]) >= 5) {
        units += 1n;
    }

    return match[1] === '-' ? -units : units;
}

function divideRoundHalfUp(numerator, denominator) {
    if (denominator === 0n) {
        return null;
    }

    const sign = (numerator < 0n) !== (denominator < 0n) ? -1n : 1n;
    const absoluteNumerator = numerator < 0n ? -numerator : numerator;
    const absoluteDenominator = denominator < 0n ? -denominator : denominator;

    return sign * ((absoluteNumerator + absoluteDenominator / 2n) / absoluteDenominator);
}

function scaledUnitsToDecimal(value, precision) {
    const sign = value < 0n ? '-' : '';
    const absoluteValue = value < 0n ? -value : value;
    const scale = 10n ** BigInt(precision);

    return `${sign}${absoluteValue / scale}.${(absoluteValue % scale).toString().padStart(precision, '0')}`;
}

function getMargin(product) {
    const hpp = toMinorUnits(product.calculated_hpp);
    const price = toMinorUnits(product.selling_price ?? product.recommended_price);

    if (hpp === null || price === null || price <= 0n) {
        return { level: 'none', label: 'Harga belum tersedia', percentage: null };
    }

    const grossMargin = price - hpp;
    const level = grossMargin * 100n >= price * 30n
        ? 'healthy'
        : grossMargin * 100n >= price * 15n
            ? 'attention'
            : 'risk';
    const absolutePercentHundredths = grossMargin * 10000n / price;
    const sign = absolutePercentHundredths < 0n ? '-' : '';
    const absolutePercent = absolutePercentHundredths < 0n
        ? -absolutePercentHundredths
        : absolutePercentHundredths;
    const percentage = `${sign}${absolutePercent / 100n},${(absolutePercent % 100n).toString().padStart(2, '0')}%`;

    return { level, label: percentage, percentage };
}

function setAlert(message, type = 'error') {
    elements.alert.textContent = message;
    elements.alert.classList.remove('hidden', 'border-rose-200', 'bg-rose-50', 'text-rose-800', 'border-amber-200', 'bg-amber-50', 'text-amber-900');
    elements.alert.classList.add(
        ...(type === 'notice'
            ? ['border-amber-200', 'bg-amber-50', 'text-amber-900']
            : ['border-rose-200', 'bg-rose-50', 'text-rose-800']),
    );
}

function clearAlert() {
    elements.alert.classList.add('hidden');
    elements.alert.textContent = '';
}

async function requestJson(url, options = {}) {
    const headers = new Headers(options.headers ?? {});
    headers.set('Accept', 'application/json');
    headers.set('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

    if (options.body) {
        headers.set('Content-Type', 'application/json');
    }

    const response = await fetch(url, {
        ...options,
        credentials: 'same-origin',
        headers,
    });
    const responseText = await response.text();
    let data = null;

    if (responseText) {
        try {
            data = JSON.parse(responseText);
        } catch {
            throw new Error('Respons server tidak dapat dibaca. Coba lagi beberapa saat.');
        }
    }

    if (!response.ok) {
        const error = new Error(data?.message ?? `Permintaan gagal (${response.status}).`);
        error.status = response.status;
        error.details = data;
        throw error;
    }

    return data;
}

function setMode({ demo, business, user = null }) {
    const previousUserId = currentUser?.id ?? null;
    currentUser = user;

    if (previousUserId !== (user?.id ?? null)) {
        currentSimulations = [];
        stopSimulationPolling();
    }

    elements.businessName.textContent = business?.name ?? (demo ? 'Kedai Pagi' : 'MarginAI');
    const categoryLabels = {
        kuliner: 'Kuliner',
        konveksi: 'Konveksi',
        kerajinan: 'Kerajinan',
        rumahan: 'Produk rumahan',
    };
    elements.businessCategory.textContent = categoryLabels[business?.category] ?? (demo ? 'Kuliner' : 'Profil usaha');
    elements.modeBadge.textContent = demo ? 'Contoh tampilan' : 'Data usaha';
    elements.modeBadge.className = demo
        ? 'rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800 dark:border-amber-900 dark:bg-amber-950/60 dark:text-amber-200'
        : 'rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-200';
    elements.loginButton.classList.toggle('hidden', Boolean(user));
    elements.logoutButton.classList.toggle('hidden', !user);
    elements.productAdd.classList.toggle('hidden', !user || demo);
    elements.exportLinks.classList.toggle('hidden', !user);
    elements.rawMaterialSection.classList.toggle('hidden', !user || demo);
    elements.laborCostSection.classList.toggle('hidden', !user || demo);
    elements.overheadCostSection.classList.toggle('hidden', !user || demo);
}

function setKpi(selector, value) {
    document.querySelector(selector).textContent = value;
}

function createSvgElement(name, attributes = {}) {
    const element = document.createElementNS('http://www.w3.org/2000/svg', name);
    Object.entries(attributes).forEach(([attribute, value]) => element.setAttribute(attribute, value));

    return element;
}

function formatTrendMonth(month) {
    const date = new Date(`${month}-01T00:00:00Z`);

    if (Number.isNaN(date.getTime())) {
        return month;
    }

    return new Intl.DateTimeFormat('id-ID', {
        month: 'short',
        timeZone: 'UTC',
    }).format(date);
}

function renderTrendChart(trends, series) {
    const card = document.createElement('article');
    card.className = 'rounded-xl border border-slate-200 p-4 dark:border-slate-800';

    const title = document.createElement('h3');
    title.className = 'text-sm font-semibold text-slate-900 dark:text-white';
    title.textContent = series.title;

    const points = trends.map((trend) => {
        const value = trend[series.field];
        const amount = value === null || value === undefined || value === '' ? null : Number(value);

        return {
            month: trend.month,
            value,
            amount: Number.isFinite(amount) ? amount : null,
        };
    });
    const validAmounts = points
        .map((point) => point.amount)
        .filter((amount) => amount !== null);

    card.append(title);

    if (validAmounts.length === 0) {
        const emptyState = document.createElement('p');
        emptyState.className = 'mt-8 rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500 dark:bg-slate-800/70 dark:text-slate-400';
        emptyState.textContent = 'Data belum tersedia untuk periode ini.';
        card.append(emptyState);

        return card;
    }

    const svg = createSvgElement('svg', {
        viewBox: '0 0 360 160',
        role: 'img',
        'aria-label': `Grafik ${series.title} selama 12 bulan terakhir`,
        class: 'mt-3 h-36 w-full text-slate-200 dark:text-slate-700',
    });
    const chart = { left: 8, right: 352, top: 10, bottom: 128 };
    const minimum = Math.min(0, ...validAmounts);
    let maximum = Math.max(0, ...validAmounts);

    if (minimum === maximum) {
        maximum = minimum + 1;
    }

    [0, 0.5, 1].forEach((ratio) => {
        const y = chart.top + (chart.bottom - chart.top) * ratio;
        svg.append(createSvgElement('line', {
            x1: chart.left,
            x2: chart.right,
            y1: y,
            y2: y,
            stroke: 'currentColor',
            'stroke-dasharray': '3 5',
            'stroke-width': '1',
        }));
    });

    const xForIndex = (index) => points.length < 2
        ? (chart.left + chart.right) / 2
        : chart.left + (chart.right - chart.left) * index / (points.length - 1);
    const yForAmount = (amount) => chart.top
        + (maximum - amount) / (maximum - minimum) * (chart.bottom - chart.top);
    let pathData = '';
    let startsSegment = true;

    points.forEach((point, index) => {
        if (point.amount === null) {
            startsSegment = true;

            return;
        }

        pathData += `${startsSegment ? 'M' : 'L'}${xForIndex(index)} ${yForAmount(point.amount)} `;
        startsSegment = false;
    });

    svg.append(createSvgElement('path', {
        d: pathData.trim(),
        fill: 'none',
        stroke: series.color,
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        'stroke-width': '2.5',
    }));

    points.forEach((point, index) => {
        if (point.amount === null) {
            return;
        }

        const marker = createSvgElement('circle', {
            cx: xForIndex(index),
            cy: yForAmount(point.amount),
            r: '3',
            fill: series.color,
        });
        const markerTitle = createSvgElement('title');
        markerTitle.textContent = `${formatTrendMonth(point.month)}: ${formatRupiah(point.value)}`;
        marker.append(markerTitle);
        svg.append(marker);
    });

    const tickIndexes = new Set([
        0,
        Math.round((points.length - 1) / 3),
        Math.round((points.length - 1) * 2 / 3),
        points.length - 1,
    ]);

    tickIndexes.forEach((index) => {
        if (!points[index]) {
            return;
        }

        const tick = createSvgElement('text', {
            x: xForIndex(index),
            y: 151,
            'text-anchor': index === 0 ? 'start' : index === points.length - 1 ? 'end' : 'middle',
            class: 'fill-slate-500 text-[10px] dark:fill-slate-400',
        });
        tick.textContent = formatTrendMonth(points[index].month);
        svg.append(tick);
    });

    card.append(svg);

    return card;
}

function renderDashboardTrends(trends) {
    const months = Array.isArray(trends) ? trends : [];

    if (!months.some((trend) => Number(trend.simulation_count ?? 0) > 0)) {
        const emptyState = document.createElement('p');
        emptyState.className = 'col-span-full rounded-xl border border-dashed border-slate-300 px-5 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400';
        emptyState.textContent = 'Belum ada simulasi harga. Buat simulasi pertama untuk melihat tren HPP, harga, dan laba di sini.';
        elements.dashboardTrends.replaceChildren(emptyState);

        return;
    }

    const series = [
        { field: 'average_hpp', title: 'HPP / unit', color: '#7c3aed' },
        { field: 'average_simulated_price', title: 'Harga simulasi / unit', color: '#0284c7' },
        { field: 'average_projected_profit', title: 'Laba proyeksi / skenario', color: '#059669' },
    ];
    elements.dashboardTrends.replaceChildren(...series.map((item) => renderTrendChart(months, item)));
}

function renderMarginSummary(products) {
    const counts = { healthy: 0, attention: 0, risk: 0 };

    products.forEach((product) => {
        const margin = getMargin(product);

        if (margin.level in counts) {
            counts[margin.level] += 1;
        }
    });

    const summaries = [
        ['healthy', 'Sehat', counts.healthy],
        ['attention', 'Perlu perhatian', counts.attention],
        ['risk', 'Risiko', counts.risk],
    ];

    elements.marginSummary.replaceChildren(...summaries.map(([level, label, count]) => {
        const item = document.createElement('div');
        item.className = 'flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70';

        const badge = document.createElement('span');
        badge.className = `inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200`;
        const dot = document.createElement('span');
        dot.className = `h-2.5 w-2.5 rounded-full ${level === 'healthy' ? 'bg-emerald-500' : level === 'attention' ? 'bg-amber-500' : 'bg-rose-500'}`;
        dot.setAttribute('aria-hidden', 'true');
        badge.append(dot, document.createTextNode(label));

        const amount = document.createElement('span');
        amount.className = 'text-sm font-bold tabular-nums text-slate-900 dark:text-white';
        amount.textContent = String(count);
        item.append(badge, amount);

        return item;
    }));
}

function getProductBatchCost(product) {
    const components = [
        ...(product.materials ?? []).map((material) => material.pivot?.subtotal_cost),
        ...(product.labor_costs ?? []).map((laborCost) => laborCost.pivot?.subtotal_cost),
        ...(product.overhead_costs ?? []).map((overheadCost) => overheadCost.pivot?.subtotal_cost),
    ].filter((cost) => cost !== null && cost !== undefined);

    if (components.length === 0) {
        return null;
    }

    return components.reduce((total, cost) => {
        const amount = decimalToScaledUnits(cost, 4);

        return total + (amount ?? 0n);
    }, 0n);
}

function renderProducts(products) {
    const searchTerm = elements.search.value.trim().toLocaleLowerCase('id-ID');
    const filteredProducts = products.filter((product) => {
        const name = String(product.name ?? '').toLocaleLowerCase('id-ID');
        const sku = String(product.sku ?? '').toLocaleLowerCase('id-ID');

        return name.includes(searchTerm) || sku.includes(searchTerm);
    });

    if (filteredProducts.length === 0) {
        const emptyState = document.createElement('p');
        emptyState.className = 'col-span-full rounded-2xl border border-dashed border-slate-300 px-5 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400';
        emptyState.textContent = products.length === 0
            ? 'Belum ada produk. Tambahkan produk dan susun formula BOM untuk menghitung HPP.'
            : 'Produk tidak ditemukan. Coba kata kunci lain.';
        elements.products.replaceChildren(emptyState);
        return;
    }

    const cards = filteredProducts.map((product) => {
        const card = document.createElement('article');
        card.className = 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900';

        const header = document.createElement('div');
        header.className = 'flex items-start justify-between gap-3';

        const titleGroup = document.createElement('div');
        titleGroup.className = 'min-w-0';
        const title = document.createElement('h3');
        title.className = 'truncate font-semibold text-slate-900 dark:text-white';
        title.textContent = product.name ?? 'Produk tanpa nama';
        const sku = document.createElement('p');
        sku.className = 'mt-1 text-xs text-slate-500 dark:text-slate-400';
        sku.textContent = product.sku ? `SKU ${product.sku}` : 'Tanpa SKU';
        titleGroup.append(title, sku);

        const margin = getMargin(product);
        const badge = document.createElement('span');
        const badgeClasses = {
            healthy: 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-200',
            attention: 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950/60 dark:text-amber-200',
            risk: 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900 dark:bg-rose-950/60 dark:text-rose-200',
            none: 'border-slate-200 bg-slate-100 text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300',
        };
        const badgeLabel = {
            healthy: 'Sehat',
            attention: 'Perlu perhatian',
            risk: 'Risiko',
            none: 'Belum ada harga',
        };
        badge.className = `shrink-0 rounded-full border px-2.5 py-1 text-xs font-semibold ${badgeClasses[margin.level]}`;
        badge.dataset.testid = 'margin-badge';
        badge.dataset.marginLevel = margin.level;
        badge.setAttribute('aria-label', margin.percentage
            ? `Margin ${margin.percentage}, ${badgeLabel[margin.level]}`
            : badgeLabel[margin.level]);
        badge.textContent = margin.percentage
            ? `${badgeLabel[margin.level]} · ${margin.percentage}`
            : badgeLabel[margin.level];
        header.append(titleGroup, badge);

        const values = document.createElement('div');
        values.className = 'mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 dark:border-slate-800';
        [
            ['HPP / unit', product.calculated_hpp],
            [product.selling_price === null || product.selling_price === undefined ? 'Rekomendasi' : 'Harga jual', product.selling_price ?? product.recommended_price],
        ].forEach(([label, value]) => {
            const detail = document.createElement('div');
            detail.className = 'min-w-0';
            const detailLabel = document.createElement('p');
            detailLabel.className = 'text-xs text-slate-500 dark:text-slate-400';
            detailLabel.textContent = label;
            const detailValue = document.createElement('p');
            detailValue.className = 'mt-1 truncate text-sm font-semibold tabular-nums text-slate-900 dark:text-white';
            detailValue.textContent = formatRupiah(value);
            detail.append(detailLabel, detailValue);
            values.append(detail);
        });

        const productionDetails = document.createElement('div');
        productionDetails.className = 'mt-3 grid grid-cols-3 gap-2 rounded-xl bg-slate-50 px-3 py-2.5 text-xs dark:bg-slate-800/70';
        const batchCost = getProductBatchCost(product);
        [
            ['Hasil / batch', product.batch_yield ? `${String(product.batch_yield).replace('.', ',')} unit` : '—'],
            ['Batch / bulan', product.batches_per_month ? String(product.batches_per_month).replace('.', ',') : '—'],
            ['Biaya / batch', formatRupiah(batchCost === null
                ? null
                : scaledUnitsToDecimal(batchCost, 4))],
        ].forEach(([label, value]) => {
            const detail = document.createElement('div');
            detail.className = 'min-w-0';
            const detailLabel = document.createElement('p');
            detailLabel.className = 'text-slate-500 dark:text-slate-400';
            detailLabel.textContent = label;
            const detailValue = document.createElement('p');
            detailValue.className = 'mt-1 truncate font-semibold tabular-nums text-slate-800 dark:text-slate-100';
            detailValue.textContent = value;
            detail.append(detailLabel, detailValue);
            productionDetails.append(detail);
        });

        card.append(header, values, productionDetails);

        if (currentUser) {
            const editButton = document.createElement('button');
            editButton.type = 'button';
            editButton.className = 'mt-3 min-h-9 w-full rounded-lg border border-slate-300 px-3 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800';
            editButton.textContent = 'Ubah produk dan BOM';
            editButton.addEventListener('click', () => openProductDialog(product));
            card.append(editButton);
        }

        return card;
    });

    elements.products.replaceChildren(...cards);
}

function renderDashboard(dashboard, products) {
    const kpis = dashboard.kpis ?? {};
    currentProducts = products;
    setKpi('#kpi-product-count', String(kpis.product_count ?? products.length));
    setKpi('#kpi-average-hpp', formatRupiah(kpis.average_hpp));
    setKpi('#kpi-average-price', formatRupiah(kpis.average_recommended_price));
    setKpi('#kpi-average-selling-price', formatRupiah(kpis.average_selling_price));
    setKpi('#kpi-average-margin', kpis.average_margin_percent === null || kpis.average_margin_percent === undefined
        ? '—'
        : `${String(kpis.average_margin_percent).replace('.', ',')}%`);
    setKpi('#kpi-average-material-cost', formatRupiah(kpis.average_material_cost_per_batch));
    setKpi('#kpi-average-labor-cost', formatRupiah(kpis.average_labor_cost_per_batch));
    setKpi('#kpi-average-overhead-cost', formatRupiah(kpis.average_overhead_cost_per_batch));
    renderDashboardTrends(dashboard.trends);
    renderProducts(currentProducts);
    renderMarginSummary(currentProducts);
    renderSimulationProductOptions(currentProducts);

    if (!currentUser) {
        currentSimulations = [];
    }

    renderSimulationHistory(currentSimulations);

    if (currentUser) {
        loadSimulationHistory();
    } else {
        stopSimulationPolling();
    }
}

function selectedSimulationProduct() {
    return currentProducts.find((product) => String(product.id) === elements.simulationProduct.value) ?? null;
}

function renderSimulationProductOptions(products) {
    const previousProductId = elements.simulationProduct.value;
    const options = products.map((product) => {
        const option = document.createElement('option');
        option.value = String(product.id);
        option.textContent = product.name ?? 'Produk tanpa nama';
        return option;
    });
    elements.simulationProduct.replaceChildren(...options);

    const hasPreviousProduct = products.some((product) => String(product.id) === previousProductId);
    const nextProductId = hasPreviousProduct ? previousProductId : (products[0] ? String(products[0].id) : '');
    elements.simulationProduct.value = nextProductId;
    elements.simulationProduct.disabled = products.length === 0;
    elements.simulationSubmit.disabled = products.length === 0;

    if (previousProductId && previousProductId !== nextProductId) {
        currentSimulations = [];
        stopSimulationPolling();
        elements.simulationResult.classList.add('hidden');
    }

    if (previousProductId !== nextProductId && nextProductId) {
        setSimulationProductDefaults();
    }

    renderSimulationMaterialChanges();
    elements.simulationAccessNote.classList.toggle('hidden', Boolean(currentUser));
    elements.simulationSubmit.textContent = currentUser ? 'Hitung simulasi' : 'Masuk untuk simulasi';

    if (products.length === 0) {
        elements.simulationMaterialChanges.textContent = 'Tambahkan produk dengan formula HPP untuk mulai membuat simulasi.';
    }
}

function setSimulationProductDefaults() {
    const product = selectedSimulationProduct();
    const price = product?.selling_price ?? product?.recommended_price ?? '';
    elements.simulationForm.elements.simulated_price.value = price;
    elements.simulationResult.classList.add('hidden');
    elements.simulationFormError.classList.add('hidden');
    elements.simulationFormError.textContent = '';
}

function renderSimulationMaterialChanges() {
    const product = selectedSimulationProduct();
    const materials = product?.materials ?? [];

    if (materials.length === 0) {
        const emptyState = document.createElement('p');
        emptyState.className = 'rounded-xl border border-dashed border-slate-300 px-3 py-3 text-xs leading-5 text-slate-500 dark:border-slate-700 dark:text-slate-400';
        emptyState.textContent = 'Produk ini belum memiliki data bahan baku untuk disimulasikan.';
        elements.simulationMaterialChanges.replaceChildren(emptyState);
        return;
    }

    const rows = materials.map((material) => {
        const row = document.createElement('label');
        row.className = 'grid grid-cols-[minmax(0,1fr)_8rem] items-center gap-3 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/70';

        const details = document.createElement('span');
        details.className = 'min-w-0';
        const name = document.createElement('span');
        name.className = 'block truncate text-sm font-medium text-slate-800 dark:text-slate-100';
        name.textContent = material.name ?? 'Bahan baku';
        const price = document.createElement('span');
        price.className = 'mt-0.5 block text-xs text-slate-500 dark:text-slate-400';
        price.textContent = `${formatRupiah(material.unit_price)} / ${material.unit ?? 'unit'}`;
        details.append(name, price);

        const inputGroup = document.createElement('span');
        inputGroup.className = 'relative';
        const input = document.createElement('input');
        input.type = 'number';
        input.min = '-100';
        input.step = '0.01';
        input.value = '0.00';
        input.dataset.materialChange = String(material.id);
        input.setAttribute('aria-label', `Perubahan harga ${material.name ?? 'bahan baku'} dalam persen`);
        input.className = 'min-h-10 w-full rounded-lg border border-slate-300 bg-white px-3 pr-8 text-right text-sm tabular-nums text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white';
        const percent = document.createElement('span');
        percent.className = 'pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400';
        percent.textContent = '%';
        inputGroup.append(input, percent);
        row.append(details, inputGroup);
        return row;
    });

    elements.simulationMaterialChanges.replaceChildren(...rows);
}

function stopSimulationPolling() {
    if (simulationPollTimer !== null) {
        window.clearTimeout(simulationPollTimer);
        simulationPollTimer = null;
    }

    simulationRequestVersion += 1;
}

function scheduleSimulationPolling(productId) {
    if (simulationPollTimer !== null) {
        window.clearTimeout(simulationPollTimer);
        simulationPollTimer = null;
    }

    if (!currentSimulations.some((simulation) => ['pending', 'processing'].includes(simulation.recommendation_status))) {
        return;
    }

    simulationPollTimer = window.setTimeout(() => {
        if (elements.simulationProduct.value === productId && currentUser) {
            loadSimulationHistory();
        }
    }, 2500);
}

async function loadSimulationHistory() {
    const product = selectedSimulationProduct();

    if (!currentUser || !product) {
        currentSimulations = [];
        renderSimulationHistory(currentSimulations);
        return;
    }

    const productId = String(product.id);
    const requestVersion = ++simulationRequestVersion;
    elements.simulationHistoryError.classList.add('hidden');
    elements.simulationHistoryError.textContent = '';

    try {
        const simulations = await requestJson(`/api/products/${productId}/price-simulations`);

        if (requestVersion !== simulationRequestVersion || elements.simulationProduct.value !== productId) {
            return;
        }

        currentSimulations = simulations;
        renderSimulationHistory(currentSimulations);

        const latestResult = simulations.find((simulation) => String(simulation.id) === elements.simulationResult.dataset.simulationId);

        if (latestResult) {
            renderSimulationResult(latestResult, product);
        }

        scheduleSimulationPolling(productId);
    } catch (error) {
        if (requestVersion !== simulationRequestVersion) {
            return;
        }

        elements.simulationHistoryError.textContent = `Riwayat simulasi tidak dapat dimuat: ${error.message}`;
        elements.simulationHistoryError.classList.remove('hidden');
        scheduleSimulationPolling(productId);
    }
}

function simulationTotalCost(simulation) {
    const revenue = toMinorUnits(simulation.projected_revenue);
    const profit = toMinorUnits(simulation.projected_profit);

    return revenue === null || profit === null
        ? null
        : scaledUnitsToDecimal(revenue - profit, 2);
}

function parseCompetitorPrices(value) {
    const prices = String(value ?? '')
        .split(/\r?\n/)
        .map((price) => price.trim())
        .filter(Boolean);

    if (prices.length > 20 || prices.some((price) => {
        if (!/^\d+(?:\.\d{1,2})?$/.test(price)) {
            return true;
        }

        return BigInt(price.replace('.', '')) === 0n;
    })) {
        return null;
    }

    return prices;
}

function simulationStatus(status) {
    const statuses = {
        pending: {
            label: 'Menunggu antrean',
            classes: 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950/50 dark:text-amber-200',
        },
        processing: {
            label: 'AI sedang bekerja...',
            classes: 'border-sky-300 bg-sky-50 text-sky-800 dark:border-sky-800 dark:bg-sky-950/60 dark:text-sky-200 animate-pulse',
        },
        completed: {
            label: 'Analisis selesai',
            classes: 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-200',
        },
        failed: {
            label: 'Analisis gagal',
            classes: 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900 dark:bg-rose-950/50 dark:text-rose-200',
        },
    };

    return statuses[status] ?? {
        label: 'Status tidak diketahui',
        classes: 'border-slate-200 bg-slate-100 text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300',
    };
}

function renderSimulationResult(simulation, product) {
    const status = simulationStatus(simulation.recommendation_status);
    const totalCost = simulationTotalCost(simulation);
    const profitIsNegative = String(simulation.projected_profit ?? '').startsWith('-');
    const resultTitle = `${product?.name ?? 'Produk'} · ${formatRupiah(simulation.simulated_price)} × ${simulation.estimated_units_sold} unit`;

    elements.simulationResultTitle.textContent = resultTitle;
    elements.simulationResultStatus.textContent = status.label;
    elements.simulationResultStatus.className = `rounded-full border px-2.5 py-1 text-xs font-semibold ${status.classes}`;
    elements.simulationResultRevenue.textContent = formatRupiah(simulation.projected_revenue);
    elements.simulationResultCost.textContent = formatRupiah(totalCost);
    elements.simulationResultProfit.textContent = formatRupiah(simulation.projected_profit);
    elements.simulationResultProfit.className = `mt-1 font-semibold tabular-nums ${profitIsNegative
        ? 'text-rose-700 dark:text-rose-300'
        : 'text-emerald-700 dark:text-emerald-300'}`;
    elements.simulationResultBep.textContent = simulation.break_even_units === null || simulation.break_even_units === undefined
        ? 'Tidak tercapai pada harga ini'
        : `${simulation.break_even_units} unit`;
    elements.simulationResultCostDetail.textContent = `Biaya variabel ${formatRupiah(simulation.variable_cost_per_unit)} / unit · biaya tetap ${formatRupiah(simulation.fixed_cost_per_period)} per periode.`;
    elements.simulationResultRecommendation.replaceChildren(renderRecommendationDetails(simulation));
    elements.simulationResult.dataset.simulationId = String(simulation.id);
    elements.simulationResult.classList.remove('hidden');
}

function renderRecommendationDetails(simulation) {
    const details = document.createElement('section');

    if (simulation.recommendation_status === 'pending') {
        details.className = 'mt-3 rounded-xl border border-amber-200 bg-amber-50/80 p-3 text-xs leading-5 text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-100';
        const title = document.createElement('p');
        title.className = 'font-semibold text-amber-950 dark:text-amber-100';
        title.textContent = 'Menunggu Antrean Analisis';
        const text = document.createElement('p');
        text.className = 'mt-0.5 text-amber-800 dark:text-amber-200';
        text.textContent = 'Rekomendasi harga dan risiko margin berada dalam antrean. Server akan segera memproses otomatis.';
        details.append(title, text);
        return details;
    }

    if (simulation.recommendation_status === 'processing') {
        details.className = 'mt-3 flex items-start gap-2.5 rounded-xl border border-sky-300 bg-sky-50/90 p-3 text-xs leading-5 text-sky-950 dark:border-sky-800 dark:bg-sky-950/50 dark:text-sky-100';

        const spinner = document.createElement('span');
        spinner.className = 'mt-0.5 inline-block h-3.5 w-3.5 shrink-0 animate-spin rounded-full border-2 border-sky-600 border-t-transparent dark:border-sky-400 dark:border-t-transparent';
        spinner.setAttribute('aria-hidden', 'true');

        const content = document.createElement('div');
        const title = document.createElement('p');
        title.className = 'font-semibold text-sky-950 dark:text-sky-100';
        title.textContent = 'Server AI Sedang Bekerja...';
        const text = document.createElement('p');
        text.className = 'mt-0.5 text-sky-800 dark:text-sky-200';
        text.textContent = 'Sedang menganalisis harga psikologis, profil margin, dan posisi kompetitor. Halaman akan diperbarui otomatis begitu hasil siap.';
        content.append(title, text);

        details.append(spinner, content);
        return details;
    }

    if (simulation.recommendation_status === 'failed') {
        details.className = 'mt-3 rounded-xl border border-rose-200 bg-rose-50/80 p-3 text-xs leading-5 text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-200';

        const content = document.createElement('div');
        const title = document.createElement('p');
        title.className = 'font-semibold text-rose-950 dark:text-rose-100';
        title.textContent = 'Analisis AI Gagal Diproses';

        const text = document.createElement('p');
        text.className = 'mt-0.5 text-rose-800 dark:text-rose-200';
        text.textContent = simulation.recommendation_error
            ? `Penyebab error: ${simulation.recommendation_error}`
            : 'Hasil analisis rekomendasi AI tidak tersedia karena terjadi kendala pada server AI.';

        const actionHint = document.createElement('p');
        actionHint.className = 'mt-1 text-[11px] text-rose-600 dark:text-rose-400';
        actionHint.textContent = 'Silakan coba buat simulasi baru untuk mengirim ulang permintaan ke AI.';

        content.append(title, text, actionHint);
        details.append(content);
        return details;
    }

    if (simulation.recommendation_status !== 'completed') {
        details.className = 'mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs leading-5 text-slate-600 dark:border-slate-700 dark:bg-slate-800/70 dark:text-slate-300';
        details.textContent = 'Hasil rekomendasi AI belum tersedia untuk simulasi ini.';
        return details;
    }

    details.className = 'mt-3 grid gap-3 rounded-xl border border-emerald-200 bg-white/80 p-3 dark:border-emerald-900 dark:bg-slate-900/70';

    const price = document.createElement('div');
    const priceLabel = document.createElement('p');
    priceLabel.className = 'text-xs font-medium text-slate-500 dark:text-slate-400';
    priceLabel.textContent = 'Harga rekomendasi AI';
    const priceValue = document.createElement('p');
    priceValue.className = 'mt-1 text-xl font-bold tabular-nums text-emerald-800 dark:text-emerald-300';
    priceValue.textContent = formatRupiah(simulation.psychological_price);
    price.append(priceLabel, priceValue);

    const notes = String(simulation.ai_notes ?? '');
    const reasonPrefix = 'Alasan harga: ';
    const riskMarker = ' Risiko margin: ';
    const riskMarkerIndex = notes.indexOf(riskMarker);
    const pricingExplanation = (riskMarkerIndex >= 0
        ? notes.slice(reasonPrefix.length, riskMarkerIndex)
        : notes.startsWith(reasonPrefix) ? notes.slice(reasonPrefix.length) : notes).trim();
    const riskExplanation = riskMarkerIndex >= 0 ? notes.slice(riskMarkerIndex + riskMarker.length).trim() : '';

    const explanation = document.createElement('div');
    explanation.className = 'grid gap-1';
    const explanationLabel = document.createElement('p');
    explanationLabel.className = 'text-xs font-semibold text-slate-700 dark:text-slate-200';
    explanationLabel.textContent = 'Alasan rekomendasi';
    const explanationText = document.createElement('p');
    explanationText.className = 'text-xs leading-5 text-slate-600 dark:text-slate-300';
    explanationText.textContent = pricingExplanation || 'Alasan rekomendasi tidak tersedia.';
    explanation.append(explanationLabel, explanationText);

    const risk = document.createElement('div');
    risk.className = 'grid gap-1';
    const riskHeading = document.createElement('div');
    riskHeading.className = 'flex flex-wrap items-center gap-2';
    const riskLabel = document.createElement('p');
    riskLabel.className = 'text-xs font-semibold text-slate-700 dark:text-slate-200';
    riskLabel.textContent = 'Risiko margin';
    const riskStates = {
        high: {
            label: 'Tinggi',
            classes: 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900 dark:bg-rose-950/50 dark:text-rose-200',
        },
        medium: {
            label: 'Perlu dipantau',
            classes: 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950/50 dark:text-amber-200',
        },
        low: {
            label: 'Terkendali',
            classes: 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-200',
        },
    };
    const riskState = riskStates[simulation.margin_risk] ?? {
        label: 'Belum tersedia',
        classes: 'border-slate-200 bg-slate-100 text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300',
    };
    const riskBadge = document.createElement('span');
    riskBadge.className = `rounded-full border px-2 py-0.5 text-[11px] font-semibold ${riskState.classes}`;
    riskBadge.textContent = riskState.label;
    riskHeading.append(riskLabel, riskBadge);
    risk.append(riskHeading);

    if (riskExplanation) {
        const riskText = document.createElement('p');
        riskText.className = 'text-xs leading-5 text-slate-600 dark:text-slate-300';
        riskText.textContent = riskExplanation;
        risk.append(riskText);
    }

    const competition = document.createElement('div');
    competition.className = 'grid gap-2 border-t border-slate-200 pt-3 dark:border-slate-800';
    const competitionLabel = document.createElement('p');
    competitionLabel.className = 'text-xs font-semibold text-slate-700 dark:text-slate-200';
    competitionLabel.textContent = 'Perbandingan kompetitor';
    competition.append(competitionLabel);

    const analysis = simulation.competition_analysis;

    if (analysis?.status === 'analyzed') {
        const positions = {
            below_competitors: 'Harga skenario berada di bawah harga kompetitor terendah.',
            within_competitor_range: 'Harga skenario berada dalam rentang harga kompetitor.',
            above_competitors: 'Harga skenario berada di atas harga kompetitor tertinggi.',
        };
        const position = document.createElement('p');
        position.className = 'text-xs leading-5 text-slate-600 dark:text-slate-300';
        position.textContent = positions[analysis.position] ?? 'Posisi harga skenario belum dapat ditentukan.';
        competition.append(position);

        const comparison = document.createElement('div');
        comparison.className = 'grid grid-cols-3 gap-2 text-xs';
        [
            ['Terendah', analysis.lowest_price],
            ['Rata-rata', analysis.average_price],
            ['Tertinggi', analysis.highest_price],
        ].forEach(([label, value]) => {
            const item = document.createElement('div');
            item.className = 'min-w-0 rounded-lg bg-slate-50 px-2 py-2 dark:bg-slate-800/70';
            const itemLabel = document.createElement('p');
            itemLabel.className = 'text-slate-500 dark:text-slate-400';
            itemLabel.textContent = label;
            const itemValue = document.createElement('p');
            itemValue.className = 'mt-1 truncate font-semibold tabular-nums text-slate-800 dark:text-slate-100';
            itemValue.textContent = formatRupiah(value);
            item.append(itemLabel, itemValue);
            comparison.append(item);
        });
        competition.append(comparison);

        const competitorCount = document.createElement('p');
        competitorCount.className = 'text-[11px] text-slate-500 dark:text-slate-400';
        competitorCount.textContent = `Berdasarkan ${analysis.competitor_count} harga kompetitor.`;
        competition.append(competitorCount);
    } else {
        const unavailable = document.createElement('p');
        unavailable.className = 'text-xs leading-5 text-slate-600 dark:text-slate-300';
        unavailable.textContent = 'Harga kompetitor belum dimasukkan, jadi perbandingan belum tersedia.';
        competition.append(unavailable);
    }

    details.append(price, explanation, risk, competition);
    return details;
}

function formatSimulationDate(value) {
    const date = new Date(value);

    return Number.isNaN(date.getTime())
        ? 'Waktu tidak tersedia'
        : new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
}

function renderSimulationHistory(simulations) {
    elements.simulationHistoryCount.textContent = String(simulations.length);

    if (!currentUser) {
        const message = document.createElement('p');
        message.className = 'rounded-xl bg-slate-50 px-3 py-3 text-xs leading-5 text-slate-500 dark:bg-slate-800/70 dark:text-slate-400';
        message.textContent = 'Masuk untuk menyimpan simulasi dan melihat riwayat analisis usaha Anda.';
        elements.simulationHistoryList.replaceChildren(message);
        return;
    }

    if (simulations.length === 0) {
        const message = document.createElement('p');
        message.className = 'rounded-xl bg-slate-50 px-3 py-3 text-xs leading-5 text-slate-500 dark:bg-slate-800/70 dark:text-slate-400';
        message.textContent = 'Belum ada riwayat untuk produk ini. Isi skenario di samping untuk memulai.';
        elements.simulationHistoryList.replaceChildren(message);
        return;
    }

    const rows = simulations.map((simulation) => {
        const article = document.createElement('article');
        article.className = 'rounded-xl border border-slate-200 p-3 dark:border-slate-800';

        const header = document.createElement('div');
        header.className = 'flex flex-wrap items-start justify-between gap-2';
        const details = document.createElement('div');
        details.className = 'min-w-0';
        const title = document.createElement('p');
        title.className = 'font-semibold text-slate-900 dark:text-white';
        title.textContent = `${formatRupiah(simulation.simulated_price)} · ${simulation.estimated_units_sold} unit`;
        const date = document.createElement('p');
        date.className = 'mt-1 text-xs text-slate-500 dark:text-slate-400';
        date.textContent = formatSimulationDate(simulation.created_at);
        details.append(title, date);

        const status = simulationStatus(simulation.recommendation_status);
        const badge = document.createElement('span');
        badge.className = `shrink-0 rounded-full border px-2 py-1 text-[11px] font-semibold ${status.classes}`;
        badge.textContent = status.label;
        header.append(details, badge);

        const summary = document.createElement('div');
        summary.className = 'mt-3 grid grid-cols-2 gap-2 text-xs';
        [
            ['Omzet', formatRupiah(simulation.projected_revenue)],
            ['Biaya', formatRupiah(simulationTotalCost(simulation))],
            ['Laba', formatRupiah(simulation.projected_profit)],
            ['BEP', simulation.break_even_units === null || simulation.break_even_units === undefined
                ? 'Tidak tercapai'
                : `${simulation.break_even_units} unit`],
        ].forEach(([label, value]) => {
            const item = document.createElement('div');
            item.className = 'min-w-0 rounded-lg bg-slate-50 px-2.5 py-2 dark:bg-slate-800/70';
            const itemLabel = document.createElement('p');
            itemLabel.className = 'text-slate-500 dark:text-slate-400';
            itemLabel.textContent = label;
            const itemValue = document.createElement('p');
            itemValue.className = 'mt-1 truncate font-semibold tabular-nums text-slate-800 dark:text-slate-100';
            itemValue.textContent = value;
            item.append(itemLabel, itemValue);
            summary.append(item);
        });

        article.append(header, summary);

        article.append(renderRecommendationDetails(simulation));

        return article;
    });

    elements.simulationHistoryList.replaceChildren(...rows);
}

function clearProductFormErrors() {
    elements.productForm.querySelectorAll('[data-field-error]').forEach((element) => {
        element.textContent = '';
        element.classList.add('hidden');
    });
    elements.productFormError.textContent = '';
    elements.productFormError.classList.add('hidden');
}

function renderProductFormulaOptions(product = null) {
    elements.productMaterialOptions.replaceChildren();
    elements.productLaborOptions.replaceChildren();
    elements.productOverheadOptions.replaceChildren();

    if (currentRawMaterials.length === 0) {
        const emptyState = document.createElement('p');
        emptyState.className = 'rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500 dark:bg-slate-800/70 dark:text-slate-400';
        emptyState.textContent = 'Tambahkan bahan baku pada bagian Master data terlebih dahulu.';
        elements.productMaterialOptions.append(emptyState);
    }

    currentRawMaterials.forEach((material) => {
        const selectedMaterial = product?.materials?.find((item) => item.id === material.id);
        const row = document.createElement('div');
        row.className = 'grid grid-cols-[auto_minmax(0,1fr)_6rem] items-center gap-3 rounded-xl bg-slate-50 p-3 dark:bg-slate-800/70';

        const checkbox = document.createElement('input');
        checkbox.id = `product-material-${material.id}`;
        checkbox.type = 'checkbox';
        checkbox.className = 'h-4 w-4 rounded border-slate-300 accent-emerald-700';
        checkbox.dataset.productMaterialSelect = String(material.id);
        checkbox.checked = Boolean(selectedMaterial);
        checkbox.setAttribute('aria-label', `Pilih ${material.name}`);

        const detail = document.createElement('div');
        detail.className = 'min-w-0';
        const label = document.createElement('label');
        label.htmlFor = checkbox.id;
        label.className = 'block truncate text-sm font-medium text-slate-800 dark:text-slate-100';
        label.textContent = material.name;
        const description = document.createElement('p');
        description.className = 'mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400';
        description.textContent = `${rawMaterialUnitLabels[material.unit] ?? material.unit} · ${formatRupiah(material.unit_price)} / ${material.unit}`;
        detail.append(label, description);

        const quantity = document.createElement('input');
        quantity.type = 'number';
        quantity.min = '0.0001';
        quantity.step = '0.0001';
        quantity.placeholder = 'Jumlah';
        quantity.value = selectedMaterial?.pivot?.quantity_used ?? '';
        quantity.disabled = !checkbox.checked;
        quantity.required = checkbox.checked;
        quantity.className = 'min-h-10 w-24 rounded-lg border border-slate-300 bg-white px-2 text-right text-sm text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white';
        quantity.dataset.productMaterialQuantity = String(material.id);

        checkbox.addEventListener('change', () => {
            quantity.disabled = !checkbox.checked;
            quantity.required = checkbox.checked;
            updateProductCostPreview();
        });
        quantity.addEventListener('input', updateProductCostPreview);
        row.append(checkbox, detail, quantity);
        elements.productMaterialOptions.append(row);
    });

    if (currentLaborCosts.length === 0) {
        const emptyState = document.createElement('p');
        emptyState.className = 'rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500 dark:bg-slate-800/70 dark:text-slate-400';
        emptyState.textContent = 'Belum ada data tenaga kerja. Bagian ini opsional.';
        elements.productLaborOptions.append(emptyState);
    }

    currentLaborCosts.forEach((laborCost) => {
        const selectedLabor = product?.labor_costs?.find((item) => item.id === laborCost.id);
        const row = document.createElement('div');
        row.className = 'grid grid-cols-[auto_minmax(0,1fr)_6rem] items-center gap-3 rounded-xl bg-slate-50 p-3 dark:bg-slate-800/70';

        const checkbox = document.createElement('input');
        checkbox.id = `product-labor-${laborCost.id}`;
        checkbox.type = 'checkbox';
        checkbox.className = 'h-4 w-4 rounded border-slate-300 accent-emerald-700';
        checkbox.dataset.productLaborSelect = String(laborCost.id);
        checkbox.checked = Boolean(selectedLabor);
        checkbox.setAttribute('aria-label', `Pilih ${laborCost.name}`);

        const detail = document.createElement('div');
        detail.className = 'min-w-0';
        const label = document.createElement('label');
        label.htmlFor = checkbox.id;
        label.className = 'block truncate text-sm font-medium text-slate-800 dark:text-slate-100';
        label.textContent = laborCost.name;
        const rateType = laborRateTypeLabels[laborCost.rate_type];
        const description = document.createElement('p');
        description.className = 'mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400';
        description.textContent = `${formatRupiah(laborCost.rate)} / ${rateType?.unit ?? 'unit'}`;
        detail.append(label, description);

        const quantity = document.createElement('input');
        quantity.type = 'number';
        quantity.min = '0.0001';
        quantity.step = '0.0001';
        quantity.placeholder = 'Jumlah';
        quantity.value = selectedLabor?.pivot?.quantity_used ?? '';
        quantity.disabled = !checkbox.checked;
        quantity.required = checkbox.checked;
        quantity.className = 'min-h-10 w-24 rounded-lg border border-slate-300 bg-white px-2 text-right text-sm text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-950 dark:text-white';
        quantity.dataset.productLaborQuantity = String(laborCost.id);

        checkbox.addEventListener('change', () => {
            quantity.disabled = !checkbox.checked;
            quantity.required = checkbox.checked;
            updateProductCostPreview();
        });
        quantity.addEventListener('input', updateProductCostPreview);
        row.append(checkbox, detail, quantity);
        elements.productLaborOptions.append(row);
    });

    if (currentOverheadCosts.length === 0) {
        const emptyState = document.createElement('p');
        emptyState.className = 'rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500 dark:bg-slate-800/70 dark:text-slate-400';
        emptyState.textContent = 'Belum ada biaya overhead. Bagian ini opsional.';
        elements.productOverheadOptions.append(emptyState);
    }

    currentOverheadCosts.forEach((overheadCost) => {
        const selectedOverhead = product?.overhead_costs?.some((item) => item.id === overheadCost.id);
        const row = document.createElement('label');
        row.className = 'flex items-center gap-3 rounded-xl bg-slate-50 p-3 dark:bg-slate-800/70';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'h-4 w-4 rounded border-slate-300 accent-emerald-700';
        checkbox.dataset.productOverheadSelect = String(overheadCost.id);
        checkbox.checked = Boolean(selectedOverhead);
        checkbox.setAttribute('aria-label', `Pilih ${overheadCost.name}`);
        checkbox.addEventListener('change', updateProductCostPreview);

        const detail = document.createElement('span');
        detail.className = 'min-w-0 flex-1';
        const name = document.createElement('span');
        name.className = 'block truncate text-sm font-medium text-slate-800 dark:text-slate-100';
        name.textContent = overheadCost.name;
        const description = document.createElement('span');
        description.className = 'mt-0.5 block truncate text-xs text-slate-500 dark:text-slate-400';
        description.textContent = `${overheadCost.type === 'fixed' ? 'Tetap · per bulan' : 'Variabel · per batch'} · ${formatRupiah(overheadCost.amount)}`;
        detail.append(name, description);
        row.append(checkbox, detail);
        elements.productOverheadOptions.append(row);
    });

    updateProductCostPreview();
}

function updateProductCostPreview() {
    const materialTotal = Array.from(elements.productMaterialOptions.querySelectorAll('[data-product-material-select]:checked'))
        .reduce((total, checkbox) => {
            const material = currentRawMaterials.find((item) => String(item.id) === checkbox.dataset.productMaterialSelect);
            const quantity = decimalToScaledUnits(
                elements.productMaterialOptions.querySelector(`[data-product-material-quantity="${checkbox.dataset.productMaterialSelect}"]`)?.value,
                4,
            );
            const unitPrice = decimalToScaledUnits(material?.unit_price, 4);
            const wastage = decimalToScaledUnits(material?.wastage_percent, 2);

            if (quantity === null || unitPrice === null || wastage === null) {
                return total;
            }

            const subtotal = divideRoundHalfUp(
                quantity * unitPrice * (10000n + wastage * 10n),
                100000000n,
            ) ?? 0n;

            return total + subtotal;
        }, 0n);

    const laborTotal = Array.from(elements.productLaborOptions.querySelectorAll('[data-product-labor-select]:checked'))
        .reduce((total, checkbox) => {
            const laborCost = currentLaborCosts.find((item) => String(item.id) === checkbox.dataset.productLaborSelect);
            const quantity = decimalToScaledUnits(
                elements.productLaborOptions.querySelector(`[data-product-labor-quantity="${checkbox.dataset.productLaborSelect}"]`)?.value,
                4,
            );
            const rate = decimalToScaledUnits(laborCost?.rate, 4);
            const subtotal = quantity === null || rate === null
                ? null
                : divideRoundHalfUp(quantity * rate, 10000n);

            return total + (subtotal ?? 0n);
        }, 0n);

    const yieldAmount = decimalToScaledUnits(elements.productForm.elements.batch_yield.value, 2);
    const batchesPerMonth = decimalToScaledUnits(elements.productForm.elements.batches_per_month.value, 2);
    const selectedOverheads = Array.from(elements.productOverheadOptions.querySelectorAll('[data-product-overhead-select]:checked'));
    const overheadTotal = selectedOverheads.reduce((total, checkbox) => {
        const overhead = currentOverheadCosts.find((item) => String(item.id) === checkbox.dataset.productOverheadSelect);
        const amount = decimalToScaledUnits(overhead?.amount, 4);

        if (amount === null) {
            return total;
        }

        if (overhead.type === 'variable') {
            return total + amount;
        }

        const linkedMonthlyBatches = currentProducts.reduce((batchTotal, product) => {
            if (product.id === editingProduct?.id
                || !product.overhead_costs?.some((item) => String(item.id) === checkbox.dataset.productOverheadSelect)) {
                return batchTotal;
            }

            return batchTotal + (decimalToScaledUnits(product.batches_per_month, 2) ?? 0n);
        }, 0n);
        const totalMonthlyBatches = linkedMonthlyBatches + (batchesPerMonth ?? 0n);
        const allocatedCost = totalMonthlyBatches <= 0n
            ? null
            : divideRoundHalfUp(amount * 100n, totalMonthlyBatches);

        return total + (allocatedCost ?? 0n);
    }, 0n);

    const batchTotal = materialTotal + laborTotal + overheadTotal;
    const hppPerUnit = yieldAmount === null || yieldAmount <= 0n
        ? null
        : divideRoundHalfUp(batchTotal, yieldAmount);

    elements.productMaterialTotal.textContent = formatRupiah(scaledUnitsToDecimal(materialTotal, 4));
    elements.productLaborTotal.textContent = formatRupiah(scaledUnitsToDecimal(laborTotal, 4));
    elements.productOverheadTotal.textContent = formatRupiah(scaledUnitsToDecimal(overheadTotal, 4));
    elements.productBatchTotal.textContent = formatRupiah(scaledUnitsToDecimal(batchTotal, 4));
    elements.productHppUnit.textContent = `HPP / unit ${hppPerUnit === null
        ? '—'
        : formatRupiah(scaledUnitsToDecimal(hppPerUnit, 2))}`;
}

function openProductDialog(product = null) {
    editingProduct = product;
    clearProductFormErrors();
    elements.productForm.reset();
    elements.productForm.elements.id.value = product?.id ?? '';
    elements.productForm.elements.name.value = product?.name ?? '';
    elements.productForm.elements.sku.value = product?.sku ?? '';
    elements.productForm.elements.batch_yield.value = product?.batch_yield ?? '';
    elements.productForm.elements.batches_per_month.value = product?.batches_per_month ?? '';
    elements.productDialogTitle.textContent = product ? 'Ubah produk dan BOM' : 'Tambah produk';
    elements.productSubmit.textContent = product ? 'Simpan perubahan' : 'Simpan produk';
    renderProductFormulaOptions(product);
    elements.productDialog.showModal();
}

function closeProductDialog() {
    elements.productDialog.close();
    editingProduct = null;
}

const rawMaterialUnitLabels = {
    gram: 'Gram',
    kg: 'Kilogram',
    meter: 'Meter',
    pcs: 'Pcs',
    liter: 'Liter',
};

function clearRawMaterialFormErrors() {
    elements.rawMaterialForm.querySelectorAll('[data-field-error]').forEach((element) => {
        element.textContent = '';
        element.classList.add('hidden');
    });
    elements.rawMaterialFormError.textContent = '';
    elements.rawMaterialFormError.classList.add('hidden');
}

function openRawMaterialDialog(material = null) {
    clearRawMaterialFormErrors();
    elements.rawMaterialForm.reset();
    elements.rawMaterialForm.elements.id.value = material?.id ?? '';
    elements.rawMaterialForm.elements.name.value = material?.name ?? '';
    elements.rawMaterialForm.elements.unit.value = material?.unit ?? '';
    elements.rawMaterialForm.elements.unit_price.value = material?.unit_price ?? '';
    elements.rawMaterialForm.elements.wastage_percent.value = material?.wastage_percent ?? '0';
    elements.rawMaterialDialogTitle.textContent = material ? 'Ubah bahan baku' : 'Tambah bahan baku';
    elements.rawMaterialSubmit.textContent = material ? 'Simpan perubahan' : 'Simpan bahan';
    elements.rawMaterialDialog.showModal();
}

function closeRawMaterialDialog() {
    elements.rawMaterialDialog.close();
}

function renderRawMaterials(materials) {
    currentRawMaterials = materials;

    if (materials.length === 0) {
        const emptyState = document.createElement('p');
        emptyState.className = 'rounded-2xl border border-dashed border-slate-300 px-5 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400';
        emptyState.textContent = 'Belum ada bahan baku. Tambahkan bahan untuk mulai menyusun formula produk.';
        elements.rawMaterialList.replaceChildren(emptyState);
        return;
    }

    const table = document.createElement('table');
    table.className = 'min-w-full border-separate border-spacing-0 text-sm';

    const head = document.createElement('thead');
    head.innerHTML = `
        <tr class="text-left text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
            <th class="border-b border-slate-200 px-3 py-3 font-semibold dark:border-slate-800">Nama</th>
            <th class="border-b border-slate-200 px-3 py-3 font-semibold dark:border-slate-800">Satuan</th>
            <th class="border-b border-slate-200 px-3 py-3 font-semibold dark:border-slate-800">Harga satuan</th>
            <th class="border-b border-slate-200 px-3 py-3 font-semibold dark:border-slate-800">Susut</th>
            <th class="border-b border-slate-200 px-3 py-3 text-right font-semibold dark:border-slate-800">Aksi</th>
        </tr>`;

    const body = document.createElement('tbody');
    materials.forEach((material) => {
        const row = document.createElement('tr');
        row.className = 'text-slate-700 dark:text-slate-200';

        const nameCell = document.createElement('td');
        nameCell.className = 'border-b border-slate-100 px-3 py-4 font-semibold text-slate-900 dark:border-slate-800 dark:text-white';
        nameCell.textContent = material.name;

        const unitCell = document.createElement('td');
        unitCell.className = 'border-b border-slate-100 px-3 py-4 dark:border-slate-800';
        unitCell.textContent = rawMaterialUnitLabels[material.unit] ?? material.unit;

        const priceCell = document.createElement('td');
        priceCell.className = 'border-b border-slate-100 px-3 py-4 tabular-nums dark:border-slate-800';
        priceCell.textContent = formatRupiah(material.unit_price);

        const wastageCell = document.createElement('td');
        wastageCell.className = 'border-b border-slate-100 px-3 py-4 tabular-nums dark:border-slate-800';
        wastageCell.textContent = `${String(material.wastage_percent).replace('.', ',')}%`;

        const actionCell = document.createElement('td');
        actionCell.className = 'border-b border-slate-100 px-3 py-3 text-right dark:border-slate-800';
        const actions = document.createElement('div');
        actions.className = 'flex justify-end gap-2';

        const editButton = document.createElement('button');
        editButton.type = 'button';
        editButton.className = 'min-h-9 rounded-lg border border-slate-300 px-3 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800';
        editButton.textContent = 'Ubah';
        editButton.addEventListener('click', () => openRawMaterialDialog(material));

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'min-h-9 rounded-lg border border-rose-200 px-3 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/40';
        deleteButton.textContent = 'Hapus';
        deleteButton.addEventListener('click', () => deleteRawMaterial(material));

        actions.append(editButton, deleteButton);
        actionCell.append(actions);
        row.append(nameCell, unitCell, priceCell, wastageCell, actionCell);
        body.append(row);
    });

    table.append(head, body);
    elements.rawMaterialList.replaceChildren(table);
}

async function loadRawMaterials() {
    const materials = await requestJson('/api/raw-materials');
    renderRawMaterials(materials);
}

async function deleteRawMaterial(material) {
    if (!window.confirm(`Hapus bahan baku "${material.name}"?`)) {
        return;
    }

    try {
        await requestJson(`/api/raw-materials/${material.id}`, { method: 'DELETE' });
        await loadRawMaterials();
        setAlert(`Bahan baku "${material.name}" berhasil dihapus.`, 'notice');
    } catch (error) {
        setAlert(error.status === 409
            ? error.message
            : `Bahan baku gagal dihapus: ${error.message}`);
    }
}

const laborRateTypeLabels = {
    per_hour: { label: 'Per jam', unit: 'jam', example: '2 jam' },
    per_day: { label: 'Per hari', unit: 'hari', example: '1 hari' },
    per_unit: { label: 'Per unit', unit: 'unit', example: '10 unit' },
    per_batch: { label: 'Per batch', unit: 'batch', example: '1 batch' },
};

function clearLaborCostFormErrors() {
    elements.laborCostForm.querySelectorAll('[data-field-error]').forEach((element) => {
        element.textContent = '';
        element.classList.add('hidden');
    });
    elements.laborCostFormError.textContent = '';
    elements.laborCostFormError.classList.add('hidden');
}

function updateLaborCostQuantityHelp() {
    const rateType = laborRateTypeLabels[elements.laborCostForm.elements.rate_type.value];
    if (!rateType) {
        elements.laborCostQuantityHelp.textContent = '';
        return;
    }

    elements.laborCostQuantityHelp.textContent = `Tarif ini dihitung per ${rateType.unit}. Di formula produk, masukkan jumlah ${rateType.unit} yang dipakai untuk membuat satu batch produk (contoh: ${rateType.example}).`;
}

function openLaborCostDialog(laborCost = null) {
    clearLaborCostFormErrors();
    elements.laborCostForm.reset();
    elements.laborCostForm.elements.id.value = laborCost?.id ?? '';
    elements.laborCostForm.elements.name.value = laborCost?.name ?? '';
    elements.laborCostForm.elements.rate_type.value = laborCost?.rate_type ?? 'per_hour';
    elements.laborCostForm.elements.rate.value = laborCost?.rate ?? '';
    elements.laborCostDialogTitle.textContent = laborCost ? 'Ubah tenaga kerja' : 'Tambah tenaga kerja';
    elements.laborCostSubmit.textContent = laborCost ? 'Simpan perubahan' : 'Simpan tenaga kerja';
    updateLaborCostQuantityHelp();
    elements.laborCostDialog.showModal();
}

function renderLaborCosts(laborCosts) {
    currentLaborCosts = laborCosts;

    if (laborCosts.length === 0) {
        const emptyState = document.createElement('p');
        emptyState.className = 'rounded-2xl border border-dashed border-slate-300 px-5 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400';
        emptyState.textContent = 'Belum ada data tenaga kerja. Tambahkan tarif untuk memasukkannya ke formula produk.';
        elements.laborCostList.replaceChildren(emptyState);
        return;
    }

    const table = document.createElement('table');
    table.className = 'min-w-full border-separate border-spacing-0 text-sm';

    const head = document.createElement('thead');
    head.innerHTML = `
        <tr class="text-left text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
            <th class="border-b border-slate-200 px-3 py-3 font-semibold dark:border-slate-800">Pekerjaan</th>
            <th class="border-b border-slate-200 px-3 py-3 font-semibold dark:border-slate-800">Tipe tarif</th>
            <th class="border-b border-slate-200 px-3 py-3 font-semibold dark:border-slate-800">Tarif</th>
            <th class="border-b border-slate-200 px-3 py-3 text-right font-semibold dark:border-slate-800">Aksi</th>
        </tr>`;

    const body = document.createElement('tbody');
    laborCosts.forEach((laborCost) => {
        const row = document.createElement('tr');
        row.className = 'text-slate-700 dark:text-slate-200';

        const nameCell = document.createElement('td');
        nameCell.className = 'border-b border-slate-100 px-3 py-4 font-semibold text-slate-900 dark:border-slate-800 dark:text-white';
        nameCell.textContent = laborCost.name;

        const typeCell = document.createElement('td');
        typeCell.className = 'border-b border-slate-100 px-3 py-4 dark:border-slate-800';
        const rateType = laborRateTypeLabels[laborCost.rate_type];
        typeCell.textContent = rateType ? `${rateType.label} (Rp/${rateType.unit})` : laborCost.rate_type;

        const rateCell = document.createElement('td');
        rateCell.className = 'border-b border-slate-100 px-3 py-4 tabular-nums dark:border-slate-800';
        rateCell.textContent = formatRupiah(laborCost.rate);

        const actionCell = document.createElement('td');
        actionCell.className = 'border-b border-slate-100 px-3 py-3 text-right dark:border-slate-800';
        const actions = document.createElement('div');
        actions.className = 'flex justify-end gap-2';

        const editButton = document.createElement('button');
        editButton.type = 'button';
        editButton.className = 'min-h-9 rounded-lg border border-slate-300 px-3 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800';
        editButton.textContent = 'Ubah';
        editButton.addEventListener('click', () => openLaborCostDialog(laborCost));

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'min-h-9 rounded-lg border border-rose-200 px-3 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/40';
        deleteButton.textContent = 'Hapus';
        deleteButton.addEventListener('click', () => deleteLaborCost(laborCost));

        actions.append(editButton, deleteButton);
        actionCell.append(actions);
        row.append(nameCell, typeCell, rateCell, actionCell);
        body.append(row);
    });

    table.append(head, body);
    elements.laborCostList.replaceChildren(table);
}

async function loadLaborCosts() {
    const laborCosts = await requestJson('/api/labor-costs');
    renderLaborCosts(laborCosts);
}

async function deleteLaborCost(laborCost) {
    if (!window.confirm(`Hapus tenaga kerja "${laborCost.name}"?`)) {
        return;
    }

    try {
        await requestJson(`/api/labor-costs/${laborCost.id}`, { method: 'DELETE' });
        await loadLaborCosts();
        setAlert(`Tenaga kerja "${laborCost.name}" berhasil dihapus.`, 'notice');
    } catch (error) {
        setAlert(`Tenaga kerja gagal dihapus: ${error.message}`);
    }
}

const overheadCostTypeLabels = {
    fixed: {
        label: 'Tetap',
        amountLabel: 'Nilai biaya per bulan (Rp)',
    },
    variable: {
        label: 'Variabel',
        amountLabel: 'Nilai biaya per batch (Rp)',
    },
};

function clearOverheadCostFormErrors() {
    elements.overheadCostForm.querySelectorAll('[data-field-error]').forEach((element) => {
        element.textContent = '';
        element.classList.add('hidden');
    });
    elements.overheadCostFormError.textContent = '';
    elements.overheadCostFormError.classList.add('hidden');
}

function updateOverheadCostAllocationHelp() {
    const type = elements.overheadCostForm.elements.type.value;
    const overheadCostType = overheadCostTypeLabels[type];
    if (!overheadCostType) {
        elements.overheadCostAllocationHelp.textContent = '';
        return;
    }

    elements.overheadCostAmountLabel.textContent = overheadCostType.amountLabel;
    elements.overheadCostAllocationHelp.textContent = type === 'fixed'
        ? 'Nilai bulanan dibagi ke total batch per bulan dari produk yang memakai biaya tetap ini. Tambahkan biaya ini ke formula produk dan atur jumlah batch per bulan.'
        : 'Nilai ini dibebankan pada setiap batch produk yang memakai biaya variabel ini, lalu dibagi dengan hasil batch untuk menghitung biaya per unit.';
}

function openOverheadCostDialog(overheadCost = null) {
    clearOverheadCostFormErrors();
    elements.overheadCostForm.reset();
    elements.overheadCostForm.elements.id.value = overheadCost?.id ?? '';
    elements.overheadCostForm.elements.name.value = overheadCost?.name ?? '';
    elements.overheadCostForm.elements.type.value = overheadCost?.type ?? 'fixed';
    elements.overheadCostForm.elements.amount.value = overheadCost?.amount ?? '';
    elements.overheadCostDialogTitle.textContent = overheadCost ? 'Ubah biaya overhead' : 'Tambah biaya overhead';
    elements.overheadCostSubmit.textContent = overheadCost ? 'Simpan perubahan' : 'Simpan biaya overhead';
    updateOverheadCostAllocationHelp();
    elements.overheadCostDialog.showModal();
}

function renderOverheadCosts(overheadCosts) {
    currentOverheadCosts = overheadCosts;

    if (overheadCosts.length === 0) {
        const emptyState = document.createElement('p');
        emptyState.className = 'rounded-2xl border border-dashed border-slate-300 px-5 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400';
        emptyState.textContent = 'Belum ada biaya overhead. Tambahkan biaya dan pilih pada formula produk agar masuk ke HPP.';
        elements.overheadCostList.replaceChildren(emptyState);
        return;
    }

    const table = document.createElement('table');
    table.className = 'min-w-full border-separate border-spacing-0 text-sm';

    const head = document.createElement('thead');
    head.innerHTML = `
        <tr class="text-left text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
            <th class="border-b border-slate-200 px-3 py-3 font-semibold dark:border-slate-800">Nama biaya</th>
            <th class="border-b border-slate-200 px-3 py-3 font-semibold dark:border-slate-800">Jenis</th>
            <th class="border-b border-slate-200 px-3 py-3 font-semibold dark:border-slate-800">Nilai</th>
            <th class="border-b border-slate-200 px-3 py-3 text-right font-semibold dark:border-slate-800">Aksi</th>
        </tr>`;

    const body = document.createElement('tbody');
    overheadCosts.forEach((overheadCost) => {
        const row = document.createElement('tr');
        row.className = 'text-slate-700 dark:text-slate-200';

        const nameCell = document.createElement('td');
        nameCell.className = 'border-b border-slate-100 px-3 py-4 font-semibold text-slate-900 dark:border-slate-800 dark:text-white';
        nameCell.textContent = overheadCost.name;

        const typeCell = document.createElement('td');
        typeCell.className = 'border-b border-slate-100 px-3 py-4 dark:border-slate-800';
        const overheadCostType = overheadCostTypeLabels[overheadCost.type];
        typeCell.textContent = overheadCostType?.label ?? overheadCost.type;

        const amountCell = document.createElement('td');
        amountCell.className = 'border-b border-slate-100 px-3 py-4 tabular-nums dark:border-slate-800';
        const amountDescription = document.createElement('span');
        amountDescription.className = 'block text-xs text-slate-500 dark:text-slate-400';
        amountDescription.textContent = overheadCostType?.amountLabel.replace('Nilai ', '').replace(' (Rp)', '') ?? '';
        const amountValue = document.createElement('span');
        amountValue.textContent = formatRupiah(overheadCost.amount);
        amountCell.append(amountValue, amountDescription);

        const actionCell = document.createElement('td');
        actionCell.className = 'border-b border-slate-100 px-3 py-3 text-right dark:border-slate-800';
        const actions = document.createElement('div');
        actions.className = 'flex justify-end gap-2';

        const editButton = document.createElement('button');
        editButton.type = 'button';
        editButton.className = 'min-h-9 rounded-lg border border-slate-300 px-3 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800';
        editButton.textContent = 'Ubah';
        editButton.addEventListener('click', () => openOverheadCostDialog(overheadCost));

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'min-h-9 rounded-lg border border-rose-200 px-3 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/40';
        deleteButton.textContent = 'Hapus';
        deleteButton.addEventListener('click', () => deleteOverheadCost(overheadCost));

        actions.append(editButton, deleteButton);
        actionCell.append(actions);
        row.append(nameCell, typeCell, amountCell, actionCell);
        body.append(row);
    });

    table.append(head, body);
    elements.overheadCostList.replaceChildren(table);
}

async function loadOverheadCosts() {
    const overheadCosts = await requestJson('/api/overhead-costs');
    renderOverheadCosts(overheadCosts);
}

async function refreshDashboardData() {
    const [dashboard, products] = await Promise.all([
        requestJson('/api/dashboard'),
        requestJson('/api/products'),
    ]);
    renderDashboard(dashboard, products);
}

async function deleteOverheadCost(overheadCost) {
    if (!window.confirm(`Hapus biaya overhead "${overheadCost.name}"?`)) {
        return;
    }

    let deleted = false;

    try {
        await requestJson(`/api/overhead-costs/${overheadCost.id}`, { method: 'DELETE' });
        deleted = true;
        await loadOverheadCosts();
        await refreshDashboardData();
        setAlert(`Biaya overhead "${overheadCost.name}" berhasil dihapus.`, 'notice');
    } catch (error) {
        setAlert(deleted
            ? `Biaya overhead berhasil dihapus, tetapi tampilan belum dapat diperbarui: ${error.message}`
            : `Biaya overhead gagal dihapus: ${error.message}`);
    }
}

function showAccessPanel(message, showVerifyButton = false) {
    elements.setupPanel.classList.add('hidden');
    elements.accessPanel.classList.remove('hidden');
    elements.accessPanel.querySelector('[data-access-message]').textContent = message;
    elements.verifyButton.classList.toggle('hidden', !showVerifyButton);
}

async function loadBusinessData(user) {
    clearAlert();
    elements.accessPanel.classList.add('hidden');
    elements.setupPanel.classList.add('hidden');
    elements.dashboardContent.classList.add('hidden');

    try {
        const [dashboard, products, rawMaterials, laborCosts, overheadCosts] = await Promise.all([
            requestJson('/api/dashboard'),
            requestJson('/api/products'),
            requestJson('/api/raw-materials'),
            requestJson('/api/labor-costs'),
            requestJson('/api/overhead-costs'),
        ]);
        const business = await requestJson('/api/business');
        setMode({ demo: false, business, user });
        renderDashboard(dashboard, products);
        renderRawMaterials(rawMaterials);
        renderLaborCosts(laborCosts);
        renderOverheadCosts(overheadCosts);
        elements.dashboardContent.classList.remove('hidden');
    } catch (error) {
        if (error.status === 403) {
            showAccessPanel(
                `Akun ${user.email} belum memiliki akses. Verifikasi alamat email Anda, lalu coba lagi.`,
                true,
            );
            setMode({ demo: false, business: null, user });
            return;
        }

        if (error.status === 404) {
            setMode({ demo: false, business: null, user });
            elements.setupPanel.classList.remove('hidden');
            return;
        }

        setAlert(error.message);
        setMode({ demo: true, business: demoDashboard.business, user });
        renderDashboard(demoDashboard, demoDashboard.products);
        elements.dashboardContent.classList.remove('hidden');
    }
}

async function checkSession() {
    try {
        const user = await requestJson('/api/auth/me');
        await loadBusinessData(user);
    } catch (error) {
        if (error.status !== 401) {
            setAlert(`Data akun tidak dapat dimuat: ${error.message}`);
        }
    }
}

elements.loginButton.addEventListener('click', () => {
    elements.loginError.textContent = '';
    elements.loginDialog.showModal();
});

document.querySelector('#login-close').addEventListener('click', () => {
    elements.loginDialog.close();
});

elements.loginForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    elements.loginError.textContent = '';
    elements.loginSubmit.disabled = true;
    elements.loginSubmit.textContent = 'Memeriksa...';

    const formData = new FormData(elements.loginForm);

    try {
        const user = await requestJson('/api/auth/login', {
            method: 'POST',
            body: JSON.stringify({
                email: formData.get('email'),
                password: formData.get('password'),
                remember: formData.has('remember'),
            }),
        });
        elements.loginDialog.close();
        await loadBusinessData(user);
    } catch (error) {
        elements.loginError.textContent = error.message;
    } finally {
        elements.loginSubmit.disabled = false;
        elements.loginSubmit.textContent = 'Masuk';
    }
});

elements.logoutButton.addEventListener('click', async () => {
    elements.logoutButton.disabled = true;

    try {
        await requestJson('/api/auth/logout', { method: 'POST' });
        clearAlert();
        elements.accessPanel.classList.add('hidden');
        elements.setupPanel.classList.add('hidden');
        setMode({ demo: true, business: demoDashboard.business });
        renderDashboard(demoDashboard, demoDashboard.products);
        elements.dashboardContent.classList.remove('hidden');
    } catch (error) {
        setAlert(`Tidak dapat keluar: ${error.message}`);
    } finally {
        elements.logoutButton.disabled = false;
    }
});

elements.search.addEventListener('input', () => renderProducts(currentProducts));

elements.simulationProduct.addEventListener('change', () => {
    stopSimulationPolling();
    currentSimulations = [];
    elements.simulationResult.classList.add('hidden');
    elements.simulationFormError.classList.add('hidden');
    elements.simulationFormError.textContent = '';
    setSimulationProductDefaults();
    renderSimulationMaterialChanges();
    renderSimulationHistory(currentSimulations);

    if (currentUser) {
        loadSimulationHistory();
    }
});

elements.simulationForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    elements.simulationFormError.classList.add('hidden');
    elements.simulationFormError.textContent = '';

    if (!currentUser) {
        elements.loginError.textContent = '';
        elements.loginDialog.showModal();
        return;
    }

    const product = selectedSimulationProduct();

    if (!product) {
        elements.simulationFormError.textContent = 'Pilih produk yang memiliki formula HPP terlebih dahulu.';
        elements.simulationFormError.classList.remove('hidden');
        return;
    }

    const materialPriceChanges = Array.from(elements.simulationMaterialChanges.querySelectorAll('[data-material-change]'))
        .filter((input) => input.value !== '')
        .map((input) => ({
            raw_material_id: Number(input.dataset.materialChange),
            change_percent: input.value,
        }));
    const competitorPrices = parseCompetitorPrices(elements.simulationForm.elements.competitor_prices.value);

    if (competitorPrices === null) {
        elements.simulationFormError.textContent = 'Masukkan maksimal 20 harga kompetitor positif, satu harga per baris, dengan maksimal dua angka desimal.';
        elements.simulationFormError.classList.remove('hidden');
        return;
    }

    const payload = {
        simulated_price: elements.simulationForm.elements.simulated_price.value,
        estimated_units_sold: elements.simulationForm.elements.estimated_units_sold.value,
        material_price_changes: materialPriceChanges,
    };

    if (competitorPrices.length > 0) {
        payload.competitor_prices = competitorPrices;
    }

    elements.simulationSubmit.disabled = true;
    elements.simulationSubmit.textContent = 'Menghitung...';

    try {
        const simulation = await requestJson(`/api/products/${product.id}/price-simulations`, {
            method: 'POST',
            body: JSON.stringify(payload),
        });
        renderSimulationResult(simulation, product);
        setAlert('Simulasi berhasil disimpan. Permintaan analisis dikirim ke server AI.', 'notice');
        await loadSimulationHistory();
    } catch (error) {
        const validationMessages = Object.values(error.details?.errors ?? {}).flat();
        elements.simulationFormError.textContent = validationMessages.length > 0
            ? validationMessages.join(' ')
            : error.message;
        elements.simulationFormError.classList.remove('hidden');
    } finally {
        elements.simulationSubmit.disabled = currentProducts.length === 0;
        elements.simulationSubmit.textContent = currentUser ? 'Hitung simulasi' : 'Masuk untuk simulasi';
    }
});

elements.productAdd.addEventListener('click', () => openProductDialog());
elements.productDialogClose.addEventListener('click', closeProductDialog);
elements.productDialogCancel.addEventListener('click', closeProductDialog);
elements.productForm.elements.batch_yield.addEventListener('input', updateProductCostPreview);
elements.productForm.elements.batches_per_month.addEventListener('input', updateProductCostPreview);

elements.productForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearProductFormErrors();

    const formData = new FormData(elements.productForm);
    const id = formData.get('id');
    const materials = Array.from(elements.productMaterialOptions.querySelectorAll('[data-product-material-select]:checked'))
        .map((checkbox) => ({
            raw_material_id: Number(checkbox.dataset.productMaterialSelect),
            quantity_used: elements.productMaterialOptions.querySelector(`[data-product-material-quantity="${checkbox.dataset.productMaterialSelect}"]`).value,
        }));
    const laborCosts = Array.from(elements.productLaborOptions.querySelectorAll('[data-product-labor-select]:checked'))
        .map((checkbox) => ({
            labor_cost_id: Number(checkbox.dataset.productLaborSelect),
            quantity_used: elements.productLaborOptions.querySelector(`[data-product-labor-quantity="${checkbox.dataset.productLaborSelect}"]`).value,
        }));
    const overheadCosts = Array.from(elements.productOverheadOptions.querySelectorAll('[data-product-overhead-select]:checked'))
        .map((checkbox) => ({
            overhead_cost_id: Number(checkbox.dataset.productOverheadSelect),
        }));

    if (materials.length === 0) {
        const fieldError = elements.productForm.querySelector('[data-field-error="materials"]');
        fieldError.textContent = 'Pilih setidaknya satu bahan baku untuk formula produk.';
        fieldError.classList.remove('hidden');
        return;
    }

    const payload = {
        name: formData.get('name'),
        sku: formData.get('sku') || null,
        batch_yield: formData.get('batch_yield'),
        batches_per_month: formData.get('batches_per_month'),
        materials,
        labor_costs: laborCosts,
        overhead_costs: overheadCosts,
    };

    elements.productSubmit.disabled = true;
    elements.productSubmit.textContent = id ? 'Menyimpan...' : 'Menambahkan...';
    let saved = false;

    try {
        await requestJson(id ? `/api/products/${id}` : '/api/products', {
            method: id ? 'PUT' : 'POST',
            body: JSON.stringify(payload),
        });
        saved = true;
        closeProductDialog();
        await refreshDashboardData();
        setAlert(id ? 'Produk dan formula BOM berhasil diperbarui.' : 'Produk dan formula BOM berhasil ditambahkan.', 'notice');
    } catch (error) {
        if (saved) {
            setAlert(`Produk berhasil disimpan, tetapi ringkasan belum dapat diperbarui: ${error.message}`);
            return;
        }

        const fieldErrors = error.details?.errors ?? {};
        let hasFieldError = false;

        Object.entries(fieldErrors).forEach(([field, messages]) => {
            const fieldName = field.startsWith('materials.') ? 'materials' : field;
            const fieldError = elements.productForm.querySelector(`[data-field-error="${fieldName}"]`);

            if (fieldError) {
                fieldError.textContent = messages.join(' ');
                fieldError.classList.remove('hidden');
                hasFieldError = true;
            }
        });

        if (!hasFieldError) {
            elements.productFormError.textContent = error.message;
            elements.productFormError.classList.remove('hidden');
        }
    } finally {
        elements.productSubmit.disabled = false;
        elements.productSubmit.textContent = id ? 'Simpan perubahan' : 'Simpan produk';
    }
});

elements.verifyButton.addEventListener('click', async () => {
    elements.verifyButton.disabled = true;
    elements.verifyButton.textContent = 'Mengirim...';

    try {
        const response = await requestJson('/api/auth/email/verification-notification', { method: 'POST' });
        showAccessPanel(response.message ?? 'Tautan verifikasi telah dikirim.');
    } catch (error) {
        setAlert(`Tautan verifikasi gagal dikirim: ${error.message}`);
    } finally {
        elements.verifyButton.disabled = false;
        elements.verifyButton.textContent = 'Kirim ulang tautan verifikasi';
    }
});

elements.businessForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = new FormData(elements.businessForm);
    const submitButton = elements.businessForm.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = 'Menyimpan...';

    try {
        await requestJson('/api/business', {
            method: 'POST',
            body: JSON.stringify({
                name: formData.get('name'),
                category: formData.get('category'),
                pricing_method: formData.get('pricing_method'),
                default_margin_percent: formData.get('default_margin_percent'),
            }),
        });
        await loadBusinessData(currentUser);
    } catch (error) {
        setAlert(`Profil usaha gagal disimpan: ${error.message}`);
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Simpan profil usaha';
    }
});

elements.businessForm.elements.pricing_method.addEventListener('change', () => {
    const isMarkup = elements.businessForm.elements.pricing_method.value === 'markup';
    const targetInput = elements.businessForm.elements.default_margin_percent;

    targetInput.max = isMarkup ? '999.99' : '99.99';
    elements.pricingTargetLabel.textContent = isMarkup ? 'Target markup (%)' : 'Target margin (%)';
    elements.pricingTargetHelp.textContent = isMarkup
        ? 'Persentase tambahan yang dihitung dari HPP.'
        : 'Persentase laba terhadap harga jual.';
});

elements.rawMaterialAdd.addEventListener('click', () => openRawMaterialDialog());
elements.rawMaterialDialogClose.addEventListener('click', closeRawMaterialDialog);
elements.rawMaterialDialogCancel.addEventListener('click', closeRawMaterialDialog);

elements.rawMaterialForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearRawMaterialFormErrors();

    const formData = new FormData(elements.rawMaterialForm);
    const id = formData.get('id');
    const payload = {
        name: formData.get('name'),
        unit: formData.get('unit'),
        unit_price: formData.get('unit_price'),
        wastage_percent: formData.get('wastage_percent'),
    };

    elements.rawMaterialSubmit.disabled = true;
    elements.rawMaterialSubmit.textContent = id ? 'Mengubah...' : 'Menambahkan...';

    try {
        await requestJson(id ? `/api/raw-materials/${id}` : '/api/raw-materials', {
            method: id ? 'PUT' : 'POST',
            body: JSON.stringify(payload),
        });
        await loadRawMaterials();
        closeRawMaterialDialog();
        setAlert(id ? 'Bahan baku berhasil diubah.' : 'Bahan baku berhasil ditambahkan.', 'notice');
    } catch (error) {
        const fieldErrors = error.details?.errors ?? {};
        let hasFieldError = false;

        Object.entries(fieldErrors).forEach(([field, messages]) => {
            const fieldError = elements.rawMaterialForm.querySelector(`[data-field-error="${field}"]`);
            if (fieldError) {
                fieldError.textContent = messages.join(' ');
                fieldError.classList.remove('hidden');
                hasFieldError = true;
            }
        });

        if (!hasFieldError) {
            elements.rawMaterialFormError.textContent = error.message;
            elements.rawMaterialFormError.classList.remove('hidden');
        }
    } finally {
        elements.rawMaterialSubmit.disabled = false;
        elements.rawMaterialSubmit.textContent = id ? 'Simpan perubahan' : 'Simpan bahan';
    }
});

elements.laborCostAdd.addEventListener('click', () => openLaborCostDialog());
elements.laborCostDialogClose.addEventListener('click', () => elements.laborCostDialog.close());
elements.laborCostDialogCancel.addEventListener('click', () => elements.laborCostDialog.close());
elements.laborCostForm.elements.rate_type.addEventListener('change', updateLaborCostQuantityHelp);

elements.laborCostForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearLaborCostFormErrors();

    const formData = new FormData(elements.laborCostForm);
    const id = formData.get('id');
    const payload = {
        name: formData.get('name'),
        rate_type: formData.get('rate_type'),
        rate: formData.get('rate'),
    };

    elements.laborCostSubmit.disabled = true;
    elements.laborCostSubmit.textContent = id ? 'Mengubah...' : 'Menambahkan...';

    try {
        await requestJson(id ? `/api/labor-costs/${id}` : '/api/labor-costs', {
            method: id ? 'PUT' : 'POST',
            body: JSON.stringify(payload),
        });
        await loadLaborCosts();
        elements.laborCostDialog.close();
        setAlert(id ? 'Data tenaga kerja berhasil diubah.' : 'Tenaga kerja berhasil ditambahkan.', 'notice');
    } catch (error) {
        const fieldErrors = error.details?.errors ?? {};
        let hasFieldError = false;

        Object.entries(fieldErrors).forEach(([field, messages]) => {
            const fieldError = elements.laborCostForm.querySelector(`[data-field-error="${field}"]`);
            if (fieldError) {
                fieldError.textContent = messages.join(' ');
                fieldError.classList.remove('hidden');
                hasFieldError = true;
            }
        });

        if (!hasFieldError) {
            elements.laborCostFormError.textContent = error.message;
            elements.laborCostFormError.classList.remove('hidden');
        }
    } finally {
        elements.laborCostSubmit.disabled = false;
        elements.laborCostSubmit.textContent = id ? 'Simpan perubahan' : 'Simpan tenaga kerja';
    }
});

elements.overheadCostAdd.addEventListener('click', () => openOverheadCostDialog());
elements.overheadCostDialogClose.addEventListener('click', () => elements.overheadCostDialog.close());
elements.overheadCostDialogCancel.addEventListener('click', () => elements.overheadCostDialog.close());
elements.overheadCostForm.elements.type.addEventListener('change', updateOverheadCostAllocationHelp);

elements.overheadCostForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearOverheadCostFormErrors();

    const formData = new FormData(elements.overheadCostForm);
    const id = formData.get('id');
    const payload = {
        name: formData.get('name'),
        type: formData.get('type'),
        amount: formData.get('amount'),
    };

    elements.overheadCostSubmit.disabled = true;
    elements.overheadCostSubmit.textContent = id ? 'Mengubah...' : 'Menambahkan...';
    let saved = false;

    try {
        await requestJson(id ? `/api/overhead-costs/${id}` : '/api/overhead-costs', {
            method: id ? 'PUT' : 'POST',
            body: JSON.stringify(payload),
        });
        saved = true;
        elements.overheadCostDialog.close();
        await loadOverheadCosts();
        await refreshDashboardData();
        setAlert(id ? 'Biaya overhead berhasil diubah.' : 'Biaya overhead berhasil ditambahkan.', 'notice');
    } catch (error) {
        if (saved) {
            setAlert(`Biaya overhead tersimpan, tetapi tampilan belum dapat diperbarui: ${error.message}`);
            return;
        }

        const fieldErrors = error.details?.errors ?? {};
        let hasFieldError = false;

        Object.entries(fieldErrors).forEach(([field, messages]) => {
            const fieldError = elements.overheadCostForm.querySelector(`[data-field-error="${field}"]`);
            if (fieldError) {
                fieldError.textContent = messages.join(' ');
                fieldError.classList.remove('hidden');
                hasFieldError = true;
            }
        });

        if (!hasFieldError) {
            elements.overheadCostFormError.textContent = error.message;
            elements.overheadCostFormError.classList.remove('hidden');
        }
    } finally {
        elements.overheadCostSubmit.disabled = false;
        elements.overheadCostSubmit.textContent = id ? 'Simpan perubahan' : 'Simpan biaya overhead';
    }
});

setMode({ demo: true, business: demoDashboard.business });
renderDashboard(demoDashboard, demoDashboard.products);

if (document.body.dataset.authenticated === 'true') {
    checkSession();
}
