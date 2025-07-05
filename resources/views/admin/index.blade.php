@extends('layouts.admin')

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
      <h2 class="mb-0">Dashboard Admin</h2>
      <p class="text-muted fs-2 mb-30">Ringkasan data sistem</p>
    </div>

    {{-- Ringkasan Entitas --}}
    <div class="container-fluid px-0 mb-30">
      <div class="row gx-3 gy-3">
        @php
          $cards = [
              [
                  'label' => 'Toko',
                  'count' => $storeCount,
                  'icon' => 'icon-home',
                  'bg' => 'primary',
                  'route' => 'admin.stores.index',
              ],
              [
                  'label' => 'Pengguna',
                  'count' => $userCount,
                  'icon' => 'icon-user',
                  'bg' => 'success',
                  'route' => 'admin.users.index',
              ],
              [
                  'label' => 'Kategori',
                  'count' => $categoryCount,
                  'icon' => 'icon-tag',
                  'bg' => 'info',
                  'route' => 'admin.categories.index',
              ],
              [
                  'label' => 'Produk',
                  'count' => $productCount,
                  'icon' => 'icon-package',
                  'bg' => 'warning',
                  'route' => 'admin.products.index',
              ],
              [
                  'label' => 'Pesanan',
                  'count' => $orderCount,
                  'icon' => 'icon-shopping-bag',
                  'bg' => 'danger',
                  'route' => 'admin.orders.index',
              ],
              [
                  'label' => 'Pembayaran',
                  'count' => $paymentCount,
                  'icon' => 'icon-credit-card',
                  'bg' => 'secondary',
                  'route' => 'admin.payments.index',
              ],
          ];
        @endphp

        @foreach ($cards as $c)
          <div class="col-12 col-md-4 col-lg-2 p-2 pb-0">
            <div class="card h-100 shadow-sm border-0 dashboard-card">
              <div class="card-body d-flex align-items-center p-4">
                <div class="icon-box bg-{{ $c['bg'] }} me-3">
                  <i class="fas {{ $c['icon'] }} text-white fs-3"></i>
                </div>
                <div>
                  <p class="text-muted mb-1">{{ $c['label'] }}</p>
                  <h6 class="mb-0 fw-bold">{{ $c['count'] }}</h6>
                  {{-- <a href="{{ route($c['route']) }}" class="btn btn-link btn-sm">Lihat Semua</a> --}}
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
            <p class="mb-1">Total</p>
            <p class="fw-bold">Rp{{ number_format($TotalAmount, 0, ',', '.') }}</p>
          </div>
          <div>
            <p class="mb-1">Pending</p>
            <p class="fw-bold">Rp{{ number_format($TotalOrderedAmount, 0, ',', '.') }}</p>
          </div>
          <div>
            <p class="mb-1">Delivered</p>
            <p class="fw-bold">Rp{{ number_format($TotalDeliveredAmount, 0, ',', '.') }}</p>
          </div>
          <div>
            <p class="mb-1">Canceled</p>
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
        {{-- <a href="{{ route('admin.orders.index') }}" class="btn btn-primary fs-5">
          <i class="fas fa-eye me-1"></i> Lihat Semua
        </a> --}}
      </div>
      <div class="table-responsive p-3">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Toko</th>
              <th>No. Pesanan</th>
              <th>User</th>
              <th>Total</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($orders as $o)
              <tr>
                <td>
                  <a href="#" class="text-decoration-none">
                    {{ optional($o->store)->name }}
                  </a>
                </td>
                <td class="fw-bold text-primary">#{{ $o->id }}</td>
                <td>{{ optional($o->user)->name }}</td>
                <td class="fw-bold">Rp{{ number_format($o->total, 0, ',', '.') }}</td>
                <td>
                  @switch($o->status)
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
            enabled: false
          },
          plotOptions: {
            bar: {
              columnWidth: '12px',
              endingShape: 'rounded'
            }
          },
          colors: ['#2377FC', '#FFA500', '#078407', '#FF0000'],
          xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
          },
          yaxis: {
            show: false
          },
          tooltip: {
            y: {
              formatter: v => 'Rp. ' + v
            }
          }
        }
      ).render();
    });
  </script>
@endpush
