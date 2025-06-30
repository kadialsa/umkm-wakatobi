@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <section class="shop-checkout container pt-90">
      <h2 class="page-title">Pengiriman & Pemesanan</h2>

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
            <span>Pengiriman & Pembayaran</span><em>Selesaikan Pesanan Anda</em>
          </span>
        </a>
        <a href="#" class="checkout-steps__item">
          <span class="checkout-steps__item-number">03</span>
          <span class="checkout-steps__item-title">
            <span>Konfirmasi</span><em>Periksa & Kirim Pesanan</em>
          </span>
        </a>
      </div>

      <form action="{{ route('cart.place.an.order') }}" method="POST">
        @csrf

        {{-- 1) Detail Alamat --}}
        <div class="mb-4 p-3 border rounded position-relative">
          <h4>Alamat Pengiriman</h4>

          @if ($address)
            <input type="hidden" name="address_id" value="{{ $address->id }}">
            <p>
              <strong>{{ $address->name }}</strong><br>
              {{ $address->address }}, {{ $address->locality }}<br>
              {{ $address->city }}, {{ $address->state }}, {{ $address->country }}<br>
              Kode Pos: {{ $address->zip }}<br>
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

            <input type="hidden" name="destination_id" id="destination_id">
            <input type="hidden" name="province_name" id="province_name">
            <input type="hidden" name="city_name" id="city_name">
            <input type="hidden" name="district_name" id="district_name">
            <input type="hidden" name="subdistrict_name" id="subdistrict_name">
            <input type="hidden" name="zip_code" id="zip_code">
          @endif
        </div>

        {{-- 2) Ringkasan Produk per Toko --}}
        @foreach (Cart::instance('cart')->content()->groupBy(fn($i) => $i->model->store_id) as $storeId => $group)
          @php $store = $group->first()->model->store; @endphp
          <div class="mb-3 p-3 border rounded">
            <h5 class="d-flex align-items-center">
              @if ($store->logo)
                <img src="{{ asset('storage/' . $store->logo) }}" alt="{{ $store->name }}" class="rounded-circle me-2"
                  style="width:32px;height:32px;object-fit:cover;">
              @else
                <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-2"
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

        <button type="submit" class="btn btn-primary w-100" {{ $address ? '' : 'disabled' }}>
          Buat Pesanan
        </button>
      </form>
    </section>
  </main>
@endsection

@push('scripts')
  <script>
    (function() {
      const input = document.getElementById('address_search');
      const resultsEl = document.getElementById('address_results');
      const optionsCtr = document.getElementById('shipping-options');
      const grandTotalEl = document.getElementById('grand_total');
      const submitBtn = document.querySelector('button[type=submit]');

      const originId = "{{ config('services.komship.origin_city_id') }}";
      const weightGram = "{{ $weight }}";
      const couriers = "jne:jnt"; // hanya JNE & J&T
      const priceParam = "lowest";

      function setHidden(id, val) {
        document.getElementById(id).value = val;
      }

      // debounce untuk pencarian desa
      let timer;
      input?.addEventListener('input', () => {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) {
          resultsEl.innerHTML = '';
          return;
        }
        timer = setTimeout(async () => {
          const res = await fetch(`/komship/search-address?q=${encodeURIComponent(q)}&limit=5`);
          const json = await res.json();
          const arr = json.data || [];
          resultsEl.innerHTML = arr.map(item => `
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

      // saat user memilih desa
      resultsEl?.addEventListener('click', async e => {
        const li = e.target.closest('li[data-id]');
        if (!li) return;

        // tampilkan di input
        input.value = `${li.dataset.subdist}, ${li.dataset.dist}, ${li.dataset.city}`;
        resultsEl.innerHTML = '';

        // simpan detail alamat
        ['destination_id', 'province_name', 'city_name', 'district_name', 'subdistrict_name', 'zip_code']
        .forEach(key => setHidden(key, li.dataset[key.replace('_', '-')]));

        // aktifkan tombol submit
        submitBtn.disabled = false;

        // ambil ongkir
        const url = `/komship/calculate-cost` +
          `?origin=${originId}` +
          `&destination=${li.dataset.id}` +
          `&weight=${weightGram}` +
          `&courier=${encodeURIComponent(couriers)}` +
          `&price=${priceParam}`;
        const resp = await fetch(url);
        const costJson = await resp.json();

        // filter hanya code 'jne' & 'jnt'
        const filtered = (costJson.data || []).filter(svc => ['jne', 'jnt'].includes(svc.code.toLowerCase()));

        // render opsi
        optionsCtr.innerHTML = '';
        filtered.forEach((svc, idx) => {
          const price = Number(svc.cost) || 0;
          const etd = svc.etd || '-';
          const rid = `ship_opt_${idx}`;

          const div = document.createElement('div');
          div.className = 'form-check mb-2';
          div.innerHTML = `
        <input class="form-check-input" type="radio"
               name="shipping_option" id="${rid}"
               value="${price}" data-service="${svc.code}">
        <label class="form-check-label d-block" for="${rid}">
          <span>${svc.code.toUpperCase()} — ${new Intl.NumberFormat('id-ID',{
            style:'currency',currency:'IDR'
          }).format(price)}</span><br>
          <small>Estimasi : (${etd})</small>
        </label>
      `;
          optionsCtr.appendChild(div);

          // default pilih pertama
          if (idx === 0) {
            div.querySelector('input').checked = true;
            setHidden('shipping_service', svc.code.toUpperCase());
            setHidden('shipping_cost', price);
            const total = {{ $totalAll }} + price;
            grandTotalEl.innerText = new Intl.NumberFormat('id-ID', {
              style: 'currency',
              currency: 'IDR'
            }).format(total);
          }
        });

        if (filtered.length === 0) {
          optionsCtr.innerHTML = '<p class="text-danger">Maaf, opsi JNE/J&T tidak tersedia.</p>';
        }

        // update total saat ganti opsi
        optionsCtr.querySelectorAll('input[name="shipping_option"]').forEach(radio => {
          radio.addEventListener('change', e => {
            const code = e.target.dataset.service.toUpperCase();
            const price = Number(e.target.value) || 0;
            setHidden('shipping_service', code);
            setHidden('shipping_cost', price);
            const total = {{ $totalAll }} + price;
            grandTotalEl.innerText = new Intl.NumberFormat('id-ID', {
              style: 'currency',
              currency: 'IDR'
            }).format(total);
          });
        });
      });
    })();
  </script>
@endpush
