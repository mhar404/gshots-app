<script setup>
import { ref, computed, onMounted } from "vue";
import dayjs from "dayjs";
import isBetween from "dayjs/plugin/isBetween";
import isSameOrBefore from "dayjs/plugin/isSameOrBefore";
import isSameOrAfter from "dayjs/plugin/isSameOrAfter";
import { useOrderStore } from "@/stores/order";

dayjs.extend(isBetween);
dayjs.extend(isSameOrBefore);
dayjs.extend(isSameOrAfter);

const orderStore = useOrderStore();

const dateFilter = ref("7days"); // 'today', '7days', '30days', 'custom'
const customStartDate = ref(dayjs().subtract(7, "day").format("YYYY-MM-DD"));
const customEndDate = ref(dayjs().format("YYYY-MM-DD"));
const groupingType = ref("auto"); // 'auto', 'daily', 'weekly'

onMounted(() => {
    orderStore.fetchOrders();
});

// Computed active date range for UI feedback
const computedDateRange = computed(() => {
    const today = dayjs();
    let start, end;
    if (dateFilter.value === "today") {
        start = today.startOf("day");
        end = today.endOf("day");
    } else if (dateFilter.value === "7days") {
        start = today.subtract(6, "day").startOf("day");
        end = today.endOf("day");
    } else if (dateFilter.value === "30days") {
        start = today.subtract(29, "day").startOf("day");
        end = today.endOf("day");
    } else if (dateFilter.value === "custom") {
        if (!customStartDate.value || !customEndDate.value) return null;
        start = dayjs(customStartDate.value).startOf("day");
        end = dayjs(customEndDate.value).endOf("day");
    }
    return { start, end };
});

// Filter data based on date filter
const filteredOrders = computed(() => {
    const range = computedDateRange.value;
    if (!range || !range.start || !range.end) return [];

    return orderStore.orders
        .filter((order) => order.status === "completed")
        .filter((order) => {
            const orderDate = dayjs(order.created_at);
            return orderDate.isBetween(range.start, range.end, null, "[]");
        });
});

// Group filtered data into daily or weekly buckets
const groupedSalesData = computed(() => {
    const data = filteredOrders.value;
    const range = computedDateRange.value;
    if (!range || !range.start || !range.end) return [];

    let isWeekly = false;
    if (groupingType.value === "weekly") {
        isWeekly = true;
    } else if (groupingType.value === "auto" && data.length > 0) {
        const diffDays = range.end.diff(range.start, "day");
        if (diffDays > 14) isWeekly = true; // Auto convert to weekly if range > 14 days
    }

    const grouping = {};
    data.forEach((order) => {
        let key;
        if (isWeekly) {
            key =
                dayjs(order.created_at).startOf("week").format("MMM D") +
                " - " +
                dayjs(order.created_at).endOf("week").format("MMM D, YYYY");
        } else {
            key = dayjs(order.created_at).format("MMM D, YYYY");
        }

        if (!grouping[key]) {
            grouping[key] = {
                label: key,
                total: 0,
                count: 0,
            };
        }
        grouping[key].total += Number(order.total);
        grouping[key].count += 1;
    });

    let result = [];
    if (!isWeekly) {
        // Zero filling for missing days in range (Daily View)
        let current = range.start.clone();
        while (current.isSameOrBefore(range.end)) {
            const key = current.format("MMM D, YYYY");
            result.push({
                label: key,
                total: grouping[key] ? grouping[key].total : 0,
                count: grouping[key] ? grouping[key].count : 0,
            });
            current = current.add(1, "day");
        }
    } else {
        // Collect weekly groups and sort chronologically
        result = Object.values(grouping);
        result.sort(
            (a, b) =>
                dayjs(a.label.split(" - ")[0]).valueOf() -
                dayjs(b.label.split(" - ")[0]).valueOf(),
        );
    }

    return result;
});

// Summaries
const totalSales = computed(() => {
    return groupedSalesData.value.reduce((sum, item) => sum + item.total, 0);
});

const highestDay = computed(() => {
    if (groupedSalesData.value.length === 0) return 0;
    return Math.max(...groupedSalesData.value.map((item) => item.total));
});

const avgSales = computed(() => {
    if (groupedSalesData.value.length === 0) return 0;
    return totalSales.value / groupedSalesData.value.length;
});

// Chart config
const chartOptions = computed(() => ({
    chart: {
        type: "area",
        fontFamily: "inherit",
        toolbar: { show: false },
        zoom: { enabled: false },
        selection: { enabled: false },
        animations: { enabled: true, easing: "easeinout", speed: 800 },
    },
    stroke: { curve: "smooth", width: 3 },
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.45,
            opacityTo: 0.05,
            stops: [20, 100],
        },
    },
    dataLabels: { enabled: false },
    xaxis: {
        categories: groupedSalesData.value.map((item) => item.label),
        labels: {
            style: { colors: "#EF4444", fontSize: "12px" },
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: {
            formatter: (value) => {
                return "₱" + value.toLocaleString();
            },
            style: { colors: "#EF4444", fontSize: "12px" },
        },
    },
    grid: {
        borderColor: "#EF4444",
        strokeDashArray: 4,
        yaxis: { lines: { show: true } },
    },
    colors: ["#EF4444"], // Blue-500
    tooltip: {
        y: {
            formatter: (val) => "₱" + val.toLocaleString(),
        },
    },
}));

const series = computed(() => [
    {
        name: "Sales",
        data: groupedSalesData.value.map((item) => item.total),
    },
]);
</script>

<template>
    <div class="relative min-h-screen py-10 px-6 font-sans bg-gray-50">
        <div
            class="absolute inset-0 bg-[url('/assets/menu-bg.jpg')] bg-cover bg-center"
        ></div>

        <!-- Dark Overlay (FIXED) -->
        <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 max-w-6xl mx-auto space-y-8">
            <div
                class="flex flex-col md:flex-row md:items-end justify-between gap-6"
            >
                <div class="space-y-3">
                    <!-- Accent badge -->
                    <div class="flex items-center gap-2">
                        <span
                            class="w-2 h-2 rounded-full bg-red-500 animate-pulse"
                        ></span>
                        <span
                            class="text-xs uppercase tracking-wider text-white/60"
                        >
                            Analytics Dashboard
                        </span>
                    </div>

                    <!-- Title -->
                    <h1
                        class="text-4xl font-bold leading-tight bg-linear-to-r from-red-500 via-orange-500 to-yellow-500 bg-clip-text text-transparent"
                    >
                        Sales Analytics
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-sm text-white/70 max-w-md leading-relaxed">
                        Track revenue performance and order analytics in real
                        time with live insights and breakdowns.
                    </p>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-3">
                    <select
                        v-model="groupingType"
                        class="bg-white/5 backdrop-blur border border-white/20 text-white text-sm py-2 px-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                        <option class="text-black" value="auto">
                            Auto Group
                        </option>
                        <option class="text-black" value="daily">Daily</option>
                        <option class="text-black" value="weekly">
                            Weekly
                        </option>
                    </select>

                    <select
                        v-model="dateFilter"
                        class="bg-white/5 backdrop-blur border border-white/20 text-white text-sm py-2 px-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                        <option class="text-black" value="today">Today</option>
                        <option class="text-black" value="7days">
                            Last 7 Days
                        </option>
                        <option class="text-black" value="30days">
                            Last 30 Days
                        </option>
                        <option class="text-black" value="custom">
                            Custom
                        </option>
                    </select>

                    <div
                        v-if="dateFilter === 'custom'"
                        class="flex gap-2 items-center text-white"
                    >
                        <input
                            type="date"
                            v-model="customStartDate"
                            class="bg-white/5 text-sm px-3 py-2 rounded-xl border border-white/20"
                        />
                        <span>to</span>
                        <input
                            type="date"
                            v-model="customEndDate"
                            class="bg-white/5 text-sm px-3 py-2 rounded-xl border border-white/20"
                        />
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                    class="bg-white/5 border border-white/10 backdrop-blur rounded-2xl p-6 shadow-lg"
                >
                    <p class="text-sm text-white">Total Sales</p>
                    <h2 class="text-2xl font-bold text-red-500 mt-2">
                        ₱{{ totalSales.toLocaleString() }}
                    </h2>
                </div>

                <div
                    class="bg-white/5 border border-white/10 backdrop-blur rounded-2xl p-6 shadow-lg"
                >
                    <p class="text-sm text-white">Highest Day</p>
                    <h2 class="text-2xl font-bold text-red-500 mt-2">
                        ₱{{ highestDay.toLocaleString() }}
                    </h2>
                </div>

                <div
                    class="bg-white/5 border border-white/10 backdrop-blur rounded-2xl p-6 shadow-lg"
                >
                    <p class="text-sm text-white">Average Sales</p>
                    <h2 class="text-2xl font-bold text-red-500 mt-2">
                        ₱{{ Math.round(avgSales).toLocaleString() }}
                    </h2>
                </div>
            </div>

            <!-- Chart -->
            <div
                class="bg-white/5 border border-white/10 backdrop-blur-md rounded-2xl p-6 shadow-lg"
            >
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg text-white font-semibold">
                        Sales Trend
                    </h2>

                    <span
                        class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full backdrop-blur-md bg-red-500/10 border border-red-400/30 text-red-300 shadow-sm"
                    >
                        {{
                            groupingType === "weekly"
                                ? "Weekly View"
                                : "Daily View"
                        }}
                    </span>
                </div>

                <apexchart
                    v-if="groupedSalesData.length"
                    type="area"
                    height="320"
                    :options="chartOptions"
                    :series="series"
                    class="text-black!"
                />

                <div
                    v-else
                    class="h-64 flex items-center justify-center text-gray-400"
                >
                    No sales data available
                </div>
            </div>

            <!-- Table -->
            <div
                class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-lg text-white/70"
            >
                <h2 class="text-lg font-semibold mb-4 text-white">
                    Sales Breakdown
                </h2>

                <table class="w-full text-sm">
                    <thead class="border-b">
                        <tr>
                            <th class="text-left py-2">Period</th>
                            <th class="text-center">Orders</th>
                            <th class="text-right">Revenue</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(item, i) in groupedSalesData"
                            :key="i"
                            class="border-b last:border-0"
                        >
                            <td class="py-3">{{ item.label }}</td>
                            <td class="text-center">{{ item.count }}</td>
                            <td class="text-right font-semibold">
                                ₱{{ item.total.toLocaleString() }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
