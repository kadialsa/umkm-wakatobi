@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <section class="shop-checkout container pt-90">
      <h2 class="page-title">Pengiriman & Pemesanan</h2>

      {{-- Langkah Checkout --}}
      <div class="checkout-steps mb-4">
        <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">01</span>
          <span class="checkout-steps__item-title">
            <span>Keranjang</span><em>Kelola Daftar Barang</em>
          </span>
        </a>
        <a href="#" class="checkout-steps__item active">
          <span class="checkout-steps__item-number">02</span>
          <span class="checkout-steps__item-title">
            <span>Pengiriman & Pemesanan</span><em>Selesaikan Pesanan Anda</em>
          </span>
        </a>
        <a href="#" class="checkout-steps__item">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Konfirmasi</span><em>Periksa & Pembayaran</em>
          </span>
        </a>
      </div>

      {{-- Form Checkout --}}
      <form action="{{ route('cart.place.an.order') }}" method="POST">
        @csrf

        {{-- 1) Detail Alamat --}}
        <div class="mb-4 p-3 border rounded position-relative">
          <h4>Alamat Pengiriman</h4>

          @if ($address)
            <input type="hidden" name="address_id" value="{{ $address->id }}">
            <input type="hidden" id="destination_id" name="destination_id" value="{{ $address->destination_id }}">

            <p>
              <strong>{{ $address->recipient_name }}</strong><br>
              {{ $address->full_address }}<br>
              {{ $address->subdistrict }}, {{ $address->district }}<br>
              {{ $address->city }}, {{ $address->province }}<br>
              Kode Pos: {{ $address->zip_code }}<br>
              Telp: {{ $address->phone }}
            </p>
          @else
            <div class="form-floating mb-2 mt-4">
              <input type="text" id="address_search" class="form-control" placeholder="Cari Desa/Kelurahan…"
                autocomplete="off" required>
              <label for="address_search">Cari Desa/Kelurahan *</label>
            </div>
            <ul id="address_results" class="list-group position-absolute w-100"
              style="z-index:1000; max-height:200px; overflow:auto;"></ul>

            <div class="row gx-2 gy-2 mt-3">
              <div class="col-md-6 mt-4">
                <div class="form-floating">
                  <input type="text" readonly class="form-control" id="destination_id" name="destination_id">
                  <label for="destination_id">ID Desa/Kelurahan</label>
                </div>
              </div>
              <div class="col-md-6 mt-4">
                <div class="form-floating">
                  <input type="text" readonly class="form-control" id="subdistrict_name" name="subdistrict_name">
                  <label for="subdistrict_name">Desa/Kelurahan</label>
                </div>
              </div>
              <div class="col-md-6 mt-4">
                <div class="form-floating">
                  <input type="text" readonly class="form-control" id="district_name" name="district_name">
                  <label for="district_name">Kecamatan</label>
                </div>
              </div>
              <div class="col-md-6 mt-4">
                <div class="form-floating">
                  <input type="text" readonly class="form-control" id="city_name" name="city_name">
                  <label for="city_name">Kota/Kabupaten</label>
                </div>
              </div>
              <div class="col-md-6 mt-4">
                <div class="form-floating">
                  <input type="text" readonly class="form-control" id="province_name" name="province_name">
                  <label for="province_name">Provinsi</label>
                </div>
              </div>
              <div class="col-md-6 mt-4">
                <div class="form-floating">
                  <input type="text" readonly class="form-control" id="zip_code" name="zip_code">
                  <label for="zip_code">Kode Pos</label>
                </div>
              </div>
              <div class="col-md-12 mt-4">
                <div class="form-floating">
                  <textarea class="form-control" id="full_address" name="full_address" style="height: 100px !important" required>{{ old('full_address') }}</textarea>
                  <label for="full_address">Alamat Lengkap</label>
                </div>
              </div>
              <div class="col-md-6 mt-4">
                <div class="form-floating">
                  <input type="text" class="form-control" id="phone_number" name="phone_number"
                    value="{{ old('phone_number') }}" required>
                  <label for="phone_number">Nomor HP</label>
                </div>
              </div>
              <div class="col-md-6 mt-4">
                <div class="form-floating">
                  <input type="text" class="form-control" id="recipient_name" name="recipient_name"
                    value="{{ old('recipient_name') }}" required>
                  <label for="recipient_name">Nama Penerima</label>
                </div>
              </div>
            </div>
          @endif
        </div>

        {{-- 2) Ringkasan Produk per Toko --}}
        @foreach (Cart::instance('cart')->content()->groupBy(fn($i) => $i->model->store_id) as $storeId => $group)
          @php $store = $group->first()->model->store; @endphp
          <div class="mb-3 p-3 border rounded">
            <h5 class="d-flex align-items-center">
              @if ($store->logo)
                <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->name }}"
                  class="rounded-circle me-2" style="width:32px;height:32px;object-fit:cover;">
              @else
                <div
                  class="rounded-circle bg-secondary text-white d-flex
                          justify-content-center align-items-center me-2"
                  style="width:32px;height:32px;font-size:.675rem;">
                  {{ Str::upper(Str::substr($store->name, 0, 1)) }}
                </div>
              @endif
              {{ $store->name }}
            </h5>
            <table class="table mb-2">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($group as $item)
                  <tr>
                    <td>{{ $item->name }} x {{ $item->qty }}</td>
                    <td class="text-end">@rupiahSymbol($item->price * $item->qty)</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endforeach

        {{-- 3) Total & Ongkir --}}
        @php
          $subAll = Cart::instance('cart')->content()->sum(fn($i) => $i->price * $i->qty);
          $taxAll = ($subAll * config('cart.tax')) / 100;
          $weight = Cart::instance('cart')->content()->sum(fn($i) => ($i->model->weight ?? 0) * $i->qty);
          $weight = $weight > 0 ? $weight : 1000;
          $totalAll = $subAll + $taxAll;
        @endphp

        <div class="mb-4 p-3 border rounded">
          <p><strong>Subtotal:</strong> @rupiahSymbol($subAll)</p>
          <p><strong>PPN ({{ config('cart.tax') }}%):</strong> @rupiahSymbol($taxAll)</p>

          <p><strong>Ongkos Kirim:</strong></p>
          <div id="shipping-options" class="mb-2"></div>
          <input type="hidden" name="shipping_service" id="shipping_service">
          <input type="hidden" name="shipping_cost" id="shipping_cost">

          <hr>
          <p><strong>Total Bayar:</strong>
            <span id="grand_total">@rupiahSymbol($totalAll)</span>
          </p>
        </div>

        <button type="submit" class="btn btn-primary w-100">
          Buat Pesanan
        </button>
      </form>
    </section>
  </main>
@endsection

@push('scripts')
  <script>
    (function() {
      const originId = "{{ config('services.komship.origin_city_id') }}";
      const weightGram = "{{ $weight }}";
      const couriers = "jne:jnt";
      const priceParam = "lowest";

      function setShipping(service, cost) {
        document.getElementById('shipping_service').value = service;
        document.getElementById('shipping_cost').value = cost;
        const subtotal = {{ $totalAll }};
        document.getElementById('grand_total').innerText =
          new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
          })
          .format(subtotal + cost);
      }

      async function renderShipping(destinationId) {
        const res = await fetch(
          `/komship/calculate-cost` +
          `?origin=${originId}` +
          `&destination=${destinationId}` +
          `&weight=${weightGram}` +
          `&courier=${encodeURIComponent(couriers)}` +
          `&price=${priceParam}`
        );
        const {
          data: opts = []
        } = await res.json();
        const container = document.getElementById('shipping-options');
        container.innerHTML = '';

        opts.forEach((svc, idx) => {
          const price = Number(svc.cost) || 0;
          const etd = svc.etd || '-';
          const code = svc.code.toUpperCase();
          const rid = `ship_opt_${idx}`;

          const div = document.createElement('div');
          div.className = 'form-check mb-2';
          div.innerHTML = `
        <input class="form-check-input" type="radio"
               name="shipping_option" id="${rid}"
               value="${price}" data-service="${code}">
        <label class="form-check-label d-block" for="${rid}">
          ${code} — ${new Intl.NumberFormat('id-ID',{
            style:'currency',currency:'IDR'
          }).format(price)}<br>
          <small>Estimasi: ${etd}</small>
        </label>
      `;
          container.appendChild(div);

          if (idx === 0) {
            div.querySelector('input').checked = true;
            setShipping(code, price);
          }

          div.querySelector('input').addEventListener('change', e => {
            setShipping(e.target.dataset.service, Number(e.target.value) || 0);
          });
        });

        if (opts.length === 0) {
          container.innerHTML = '<p class="text-danger">Opsi kurir tidak tersedia.</p>';
        }
      }

      // Address search & selection (untuk alamat baru)
      const input = document.getElementById('address_search');
      const resultsEl = document.getElementById('address_results');
      const submitBtn = document.querySelector('button[type=submit]');
      let debounce;

      input?.addEventListener('input', () => {
        clearTimeout(debounce);
        const q = input.value.trim();
        if (q.length < 2) {
          resultsEl.innerHTML = '';
          return;
        }
        debounce = setTimeout(async () => {
          const res = await fetch(`/komship/search-address?q=${encodeURIComponent(q)}&limit=5`);
          const json = await res.json();
          resultsEl.innerHTML = (json.data || []).map(item => `
        <li class="list-group-item list-group-item-action"
            data-id="${item.id}"
            data-prov="${item.province_name}"
            data-city="${item.city_name}"
            data-dist="${item.district_name}"
            data-subdist="${item.subdistrict_name}"
            data-zip="${item.zip_code}">
          ${item.subdistrict_name}, ${item.district_name}, ${item.city_name}
        </li>
      `).join('');
        }, 300);
      });

      resultsEl?.addEventListener('click', async e => {
        const li = e.target.closest('li[data-id]');
        if (!li) return;

        input.value = `${li.dataset.subdist}, ${li.dataset.dist}, ${li.dataset.city}`;
        resultsEl.innerHTML = '';

        // set all hidden inputs
        document.getElementById('destination_id').value = li.dataset.id;
        document.getElementById('subdistrict_name').value = li.dataset.subdist;
        document.getElementById('district_name').value = li.dataset.dist;
        document.getElementById('city_name').value = li.dataset.city;
        document.getElementById('province_name').value = li.dataset.prov;
        document.getElementById('zip_code').value = li.dataset.zip;

        submitBtn.disabled = false;
        renderShipping(li.dataset.id);
      });

      // Jika alamat sudah ada, langsung hitung ongkir
      document.addEventListener('DOMContentLoaded', () => {
        const destEl = document.getElementById('destination_id');
        if (destEl && destEl.value) {
          renderShipping(destEl.value);
        }
      });
    })();
  </script>
@endpush
