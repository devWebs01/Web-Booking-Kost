<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-slot name="header">
        <li class="breadcrumb-item active">
            <a href="{{ route("home") }}">Home</a>
        </li>
    </x-slot>

    @include("components.partials.datatables")

    <div>
        <div class="card mb-4">
            <div class="card-header">
                <h5>Grafik Booking Kamar Kost (30 Hari Terakhir)</h5>
            </div>
            <div class="card-body">
                <canvas id="bookingChart" height="100"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Booking Baru</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Pengguna</th>
                            <th>Tipe Booking</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentBookings as $booking)
                            <tr>
                                <td>{{ $booking->created_at->format("d M Y H:i") }}</td>
                                <td>{{ $booking->user->name }}</td>
                                <td>{{ __("type." . $booking->booking_type) }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm">
                                        {{ __("booking." . $booking->status) }}
                                    </button>
                                </td>
                                <td>{{ formatRupiah($booking->price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <script>
        const ctx = document.getElementById('bookingChart').getContext('2d');
        const bookingData = @json($bookingCounts);

        const labels = bookingData.map(item => item.date);
        const dataCounts = bookingData.map(item => item.count);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Booking',
                    data: dataCounts,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            tooltipFormat: 'dd MMM yyyy'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>
</x-admin-layout>
