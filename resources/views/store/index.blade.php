@extends('layouts.store')

@push('styles')
  <style>
    .table-responsive {
      font-size: 14px !important;
      max-height: 400px;
      overflow-y: auto;
    }

    .gap-5 {
      gap: 9rem !important;
    }

    .dashboard-card {
      transition: transform .15s ease-in-out;
    }

    .dashboard-card:hover {
      transform: translateY(-2px);
    }

    .icon-box {
      width: 45px;
      height: 45px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .table-hover tbody tr:hover {
      background-color: rgba(0, 0, 0, .03);
    }
  </style>
@endpush

@section('content')
  <div class="main-content-inner">

    {{-- Page Header --}}
    <div class="mb-4">
      <h2 class="mb-0">Dashboard Toko</h2>
      <p class="text-muted fs-2 mb-30">Ringkasan aktivitas toko Anda</p>
    </div>

    {{-- Ringkasan Kartu --}}
    <div class="container-fluid px-0 mb-30">
      <div class="row gx-3 gy-3">
        @php
          $cards = [
              [
                  'icon' => 'fa-shopping-bag',
                  'bg' => 'primary',
                  'label' => 'Total Pesanan',
                  'value' => $dashboardDatas->Total,
              ],
              [
                  'icon' => 'fa-money-bill-wave',
                  'bg' => 'success',
                  'label' => 'Total Pendapatan',
                  'value' => 'Rp ' . number_format($dashboardDatas->TotalAmount, 0, ',', '.'),
              ],
              [
                  'icon' => 'fa-hourglass-start',
                  'bg' => 'warning',
                  'label' => 'Pesanan Menunggu',
                  'value' => $dashboardDatas->TotalOrdered,
              ],
              [
                  'icon' => 'fa-wallet',
                  'bg' => 'info',
                  'label' => 'Jumlah Menunggu',
                  'value' => 'Rp ' . number_format($dashboardDatas->TotalOrderedAmount, 0, ',', '.'),
              ],
              [
                  'icon' => 'fa-truck',
                  'bg' => 'success',
                  'label' => 'Pesanan Terkirim',
                  'value' => $dashboardDatas->TotalDelivered,
              ],
              [
                  'icon' => 'fa-coins',
                  'bg' => 'primary',
                  'label' => 'Jumlah Terkirim',
                  'value' => 'Rp ' . number_format($dashboardDatas->TotalDeliveredAmount, 0, ',', '.'),
              ],
              [
                  'icon' => 'fa-times-circle',
                  'bg' => 'danger',
                  'label' => 'Pesanan Dibatalkan',
                  'value' => $dashboardDatas->TotalCanceled,
              ],
              [
                  'icon' => 'fa-dollar-sign',
                  'bg' => 'secondary',
                  'label' => 'Jumlah Dibatalkan',
                  'value' => 'Rp ' . number_format($dashboardDatas->TotalCanceledAmount, 0, ',', '.'),
              ],
          ];
        @endphp

        @foreach ($cards as $card)
          <div class="col-12 col-md-3 p-2 pb-0">
            <div class="card h-100 shadow-sm border-0 dashboard-card">
              <div class="card-body d-flex align-items-center p-4">
                <div class="icon-box bg-{{ $card['bg'] }} me-3">
                  <i class="fas {{ $card['icon'] }} text-white fs-3"></i>
                </div>
                <div>
                  <p class="text-muted mb-1">{{ $card['label'] }}</p>
                  <h6 class="mb-0 fw-bold">{{ $card['value'] }}</h6>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Grafik Bulanan --}}
    <div class="card mb-30">
      <div class="card-header bg-white">
        <h5 class="mb-0">Pendapatan Bulanan</h5>
      </div>
      <div class="card-body">
        <div class="d-flex flex-wrap mb-4 gap-5">
          <div>
            <p class="">Total</p>
            <p class="fw-bold">Rp{{ number_format($TotalAmount, 0, ',', '.') }}</p>
          </div>
          <div>
            <p class="">Pending</p>
            <p class="fw-bold">Rp{{ number_format($TotalOrderedAmount, 0, ',', '.') }}</p>
          </div>
          <div>
            <p class="">Delivered</p>
            <p class="fw-bold">Rp{{ number_format($TotalDeliveredAmount, 0, ',', '.') }}</p>
          </div>
          <div>
            <p class="">Canceled</p>
            <p class="fw-bold">Rp{{ number_format($TotalCanceledAmount, 0, ',', '.') }}</p>
          </div>
        </div>
        {{-- **Hanya satu elemen chart** --}}
        <div id="line-chart-pendapatan" style="height:300px;"></div>
      </div>
    </div>

    {{-- Recent Orders (ringkas) --}}
    <div class="card mb-30">
      <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">Pesanan Terbaru</h5>
        <a href="{{ route('store.orders.index') }}" class="btn btn-primary fs-4">
          <i class="fas fa-eye me-1"></i> Lihat Semua
        </a>
      </div>
      <div class="table-responsive p-3">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>No. Pesanan</th>
              <th>Penerima</th>
              <th>Total</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($orders as $order)
              <tr>
                <td class="fw-bold text-primary">#{{ $order->id }}</td>
                <td>{{ $order->recipient_name }}</td>
                <td class="fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                <td>
                  @switch($order->status)
                    @case('ordered')
                      <span class="badge bg-warning">Dipesan</span>
                    @break

                    @case('shipped')
                      <span class="badge bg-info">Dikirim</span>
                    @break

                    @case('delivered')
                      <span class="badge bg-success">Terkirim</span>
                    @break

                    @case('completed')
                      <span class="badge bg-primary">Selesai</span>
                    @break

                    @case('canceled')
                      <span class="badge bg-danger">Dibatalkan</span>
                    @break
                  @endswitch
                </td>
                <td class="align-middle text-center">
                  <a href="{{ route('store.orders.show', $order->id) }}"
                    class="btn btn-outline-primary d-inline-flex align-items-center fs-5 fw-bold"
                    title="Lihat Detail Pesanan">
                    <i class="fas fa-eye me-2"></i>
                    Detail
                  </a>
                </td>

              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>


  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      new ApexCharts(
        document.querySelector("#line-chart-pendapatan"), {
          series: [{
              name: 'Total',
              data: [{{ $AmountM }}]
            },
            {
              name: 'Pending',
              data: [{{ $OrderedAmountM }}]
            },
            {
              name: 'Delivered',
              data: [{{ $DeliveredAmountM }}]
            },
            {
              name: 'Canceled',
              data: [{{ $CanceledAmountM }}]
            }
          ],
          chart: {
            type: 'bar',
            height: 300,
            toolbar: {
              show: false
            }
          },
          dataLabels: {
            enabled: false // ← sembunyikan angka di atas batang
          },
          plotOptions: {
            bar: {
              horizontal: false,
              columnWidth: '12px',
              endingShape: 'rounded'
            }
          },
          colors: ['#2377FC', '#FFA500', '#078407', '#FF0000'],
          xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
          },
          yaxis: {
            show: false // sdh sembunyikan skala vertikal
          },
          tooltip: {
            y: {
              formatter: v => 'Rp. ' + v // tooltip tetap menampilkan nominal
            }
          }
        }
      ).render();
    });
  </script>
@endpush
