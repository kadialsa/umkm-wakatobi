@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <section class="my-account container">
      <h2 class="page-title">Tambah Alamat</h2>
      <div class="row">
        <div class="col-lg-3">
          @include('user.account-nav')
        </div>
        <div class="col-lg-9">
          <div class="card mb-5">
            <div class="card-header">
              <h5>Alamat Baru</h5>
            </div>
            <div class="card-body">
              <form action="{{ route('user.address.store') }}" method="POST">
                @csrf

                {{-- Pencarian Desa/Kelurahan --}}
                <div class="form-floating mb-3 position-relative">
                  <input type="text" id="address_search" class="form-control" placeholder="Cari Desa/Kelurahan…"
                    autocomplete="off" required>
                  <label for="address_search">Cari Desa/Kelurahan *</label>
                  <ul id="address_results" class="list-group position-absolute w-100"
                    style="z-index:1000; max-height:200px; overflow:auto;"></ul>
                </div>

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

                <div class="text-end mt-4">
                  <button type="submit" class="btn btn-success">Simpan</button>
                  <a href="{{ route('user.address.index') }}" class="btn btn-secondary">Batal</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const input = document.getElementById('address_search');
      const resultsEl = document.getElementById('address_results');
      let debounce;

      input.addEventListener('input', () => {
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

      resultsEl.addEventListener('click', e => {
        const li = e.target.closest('li[data-id]');
        if (!li) return;

        // isi field pencarian
        input.value = `${li.dataset.subdist}, ${li.dataset.dist}, ${li.dataset.city}`;
        resultsEl.innerHTML = '';

        // isi hidden inputs
        document.getElementById('destination_id').value = li.dataset.id;
        document.getElementById('province_name').value = li.dataset.prov;
        document.getElementById('city_name').value = li.dataset.city;
        document.getElementById('district_name').value = li.dataset.dist;
        document.getElementById('subdistrict_name').value = li.dataset.subdist;
        document.getElementById('zip_code').value = li.dataset.zip;

      });
    });
  </script>
@endpush
