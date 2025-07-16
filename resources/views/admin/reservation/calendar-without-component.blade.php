<x-layouts.app>
    <div class="max-w-7xl mx-auto space-y-6">
        <x-features.reservation.calendar.header :view="$view ?? 'month'" />
        <!-- Calendar Navigation -->
        <div class="bg-white rounded-lg shadow-sm sm:p-6 p-0 space-y-4">
            <x-features.reservation.calendar.navigation 
                :view="$view ?? 'month'"
                :currentMonth="$currentMonth ?? now()"
                :previousMonth="$previousMonth ?? now()->subMonth()->format('Y-m')"
                :nextMonth="$nextMonth ?? now()->addMonth()->format('Y-m')"
                :startOfWeek="$startOfWeek ?? now()->startOfWeek()"
                :endOfWeek="$endOfWeek ?? now()->endOfWeek()"
                :previousWeek="$previousWeek ?? now()->subWeek()->format('Y-m-d')"
                :nextWeek="$nextWeek ?? now()->addWeek()->format('Y-m-d')"
                :currentDate="$currentDate ?? now()"
                :previousDay="$previousDay ?? now()->subDay()->format('Y-m-d')"
                :nextDay="$nextDay ?? now()->addDay()->format('Y-m-d')"
            />
            <x-features.reservation.calendar.container 
                :view="$view ?? 'month'"
                :calendarDays="$calendarDays ?? []"
                :weekDays="$weekDays ?? []"
                :hourlySlots="$hourlySlots ?? []"
                :blockedHours="$blockedHours ?? []"
                :blockedPeriods="$blockedPeriods ?? []"
                :currentDate="$currentDate ?? now()"
                :menus="$menus ?? []"
            />
            
        </div>
        <!-- Loading State -->
    <div 
        x-data="{ loading: false }"
        x-show="loading"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-40"
        style="display: none;"
    >
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Loading data...</span>
            </div>
        </div>
    </div>
    <!-- Scripts for Enhanced Functionality -->
    <script>
        setInterval(() => {
            const currentUrl = new URL(window.location.href);
            const view = currentUrl.searchParams.get('view') || 'month';
            const date = currentUrl.searchParams.get('date') || new Date().toISOString().split('T')[0];
            if (!document.querySelector('[x-show="showModal"]').style.display !== 'none' && 
                !document.querySelector('[x-show="showTooltip"]')) {
                console.log('Auto-refresh triggered');
            }
        }, 30000);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.dispatchEvent(new CustomEvent('close-modals'));
            }
            if (e.key === 'ArrowLeft' && e.ctrlKey) {
                const prevButton = document.querySelector('a[href*="previous"]');
                if (prevButton) prevButton.click();
            }
            if (e.key === 'ArrowRight' && e.ctrlKey) {
                const nextButton = document.querySelector('a[href*="next"]');
                if (nextButton) nextButton.click();
            }
        });
        let touchStartX = 0;
        let touchEndX = 0;
        document.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });
        document.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    const nextButton = document.querySelector('a[href*="next"]');
                    if (nextButton) nextButton.click();
                } else {
                    const prevButton = document.querySelector('a[href*="previous"]');
                    if (prevButton) prevButton.click();
                }
            }
        }
        function printCalendar() {
            const printWindow = window.open('', '_blank');
            const calendarContent = document.querySelector('.calendar-container').innerHTML;
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Reservation Calendar</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; }
                            .no-print { display: none !important; }
                            @media print {
                                body { margin: 0; }
                                .tooltip { display: none !important; }
                            }
                        </style>
                    </head>
                    <body>
                        <h1>Reservation Calendar</h1>
                        ${calendarContent}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
        function exportCalendar() {
            const currentUrl = new URL(window.location.href);
            const view = currentUrl.searchParams.get('view') || 'month';
            const date = currentUrl.searchParams.get('date') || new Date().toISOString().split('T')[0];
            window.location.href = `/admin/reservations/calendar/export?view=${view}&date=${date}`;
        }
    </script>
</x-layouts.app>