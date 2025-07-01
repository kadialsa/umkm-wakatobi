<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="author" content="surfside media" />
  <link rel="stylesheet" type="text/css" href="{{ asset('css/animate.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/animation.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('font/fonts.css') }}">
  <link rel="stylesheet" href="{{ asset('icon/style.css') }}">
  <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
  <link rel="apple-touch-icon-precomposed" href="{{ asset('images/favicon.ico') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/sweetalert.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  {{-- bootstrap --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

  @stack('styles')
</head>

<body class="body">
  <div id="wrapper">
    <div id="page" class="">
      <div class="layout-wrap">
        <div class="section-menu-left">
          <div class="box-logo">
            <a href="{{ route('admin.index') }}" id="site-logo-inner">
              <img class="" id="logo_header_1" alt="" src="{{ asset('images\logo\logo-UMKM.png') }}"
                data-light="{{ asset('images\logo\logo-UMKM.png') }}"
                data-dark="{{ asset('images\logo\logo-UMKM.png') }}">
            </a>
            <div class="button-show-hide">
              <i class="icon-menu-left"></i>
            </div>
          </div>
          <div class="center">
            <div class="center-item">
              <div class="center-heading">Main Home</div>
              <ul class="menu-list">
                <li class="menu-item">
                  <a href="{{ route('admin.index') }}" class="">
                    <div class="icon"><i class="icon-home"></i></div>
                    <div class="text">Dashboard</div>
                  </a>
                </li>
              </ul>
            </div>

            <div class="center-item">
              <ul class="menu-list">
                {{-- <li class="menu-item has-children">
                  <a href="javascript:void(0);" class="menu-item-button">
                    <div class="icon"><i class="icon-shopping-cart"></i></div>
                    <div class="text">Products</div>
                  </a>
                  <ul class="sub-menu">
                    <li class="sub-menu-item">
                      <a href="{{ route('admin.product.add') }}" class="">
                        <div class="text">Add Product</div>
                      </a>
                    </li>
                    <li class="sub-menu-item">
                      <a href="{{ route('admin.products') }}" class="">
                        <div class="text">Products</div>
                      </a>
                    </li>
                  </ul>
                </li> --}}

                {{-- <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-layers"></i></div>
                                        <div class="text">Brand</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.brand.add') }}" class="">
                                                <div class="text">New Brand</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.brands') }}" class="">
                                                <div class="text">Brands</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li> --}}

                <li class="menu-item has-children">
                  <a href="javascript:void(0);" class="menu-item-button">
                    <div class="icon"><i class="icon-layers"></i></div>
                    <div class="text">Category</div>
                  </a>
                  <ul class="sub-menu">
                    <li class="sub-menu-item">
                      <a href="{{ route('admin.category.add') }}" class="">
                        <div class="text">New Category</div>
                      </a>
                    </li>
                    <li class="sub-menu-item">
                      <a href="{{ route('admin.categories') }}" class="">
                        <div class="text">Categories</div>
                      </a>
                    </li>
                  </ul>
                </li>

                {{-- <li class="menu-item has-children">
                  <a href="javascript:void(0);" class="menu-item-button">
                    <div class="icon"><i class="icon-file-plus"></i></div>
                    <div class="text">Order</div>
                  </a>
                  <ul class="sub-menu">
                    <li class="sub-menu-item">
                      <a href="{{ route('admin.orders') }}" class="">
                        <div class="text">Orders</div>
                      </a>
                    </li>
                    <li class="sub-menu-item">
                      <a href="order-tracking.html" class="">
                        <div class="text">Order tracking</div>
                      </a>
                    </li>
                  </ul>
                </li> --}}

                {{-- adress --}}
                <li class="menu-item has-children">
                  <a href="javascript:void(0);" class="menu-item-button">
                    <div class="icon"><i class="icon-map"></i></div>
                    <div class="text">Address</div>
                  </a>
                  <ul class="sub-menu">
                    <li class="sub-menu-item">
                      <a href="{{ route('admin.address.add') }}" class="">
                        <div class="text">New Addres</div>
                      </a>
                    </li>
                    <li class="sub-menu-item">
                      <a href="{{ route('admin.address') }}" class="">
                        <div class="text">Address</div>
                      </a>
                    </li>
                  </ul>
                </li>


                {{-- adress --}}
                <li class="menu-item has-children">
                  <a href="javascript:void(0);" class="menu-item-button">
                    <div class="icon">
                      <i class="icon-grid"></i>
                    </div>
                    <div class="text">Blogs</div>
                  </a>
                  <ul class="sub-menu">
                    <li class="sub-menu-item">
                      <a href="{{ route('blog.create') }}" class="">
                        <div class="text">New Blog</div>
                      </a>
                    </li>
                    <li class="sub-menu-item">
                      <a href="{{ route('blog.index') }}" class="">
                        <div class="text">Blogs</div>
                      </a>
                    </li>
                  </ul>
                </li>

                <li class="menu-item">
                  <a href="#" class="">
                    <div class="icon">
                      <i class="icon-user"></i>
                    </div>
                    <div class="text">Users</div>
                  </a>
                </li>

                <li class="menu-item">
                  <a href="{{ route('admin.stores') }}" class="">
                    <div class="icon">
                      <i class="icon-shopping-bag"></i>
                    </div>
                    <div class="text">Stores</div>
                  </a>
                </li>

                <li class="menu-item">
                  <a href="{{ route('admin.slides') }}" class="">
                    <div class="icon"><i class="icon-image"></i></div>
                    <div class="text">Slides</div>
                  </a>
                </li>

                {{-- <li class="menu-item">
                  <a href="{{ route('admin.coupons') }}" class="">
                    <div class="icon"><i class="icon-grid"></i></div>
                    <div class="text">Coupns</div>
                  </a>
                </li> --}}

                <li class="menu-item mt-5">
                  <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <button type="submit" class="btn btn-danger d-flex align-items-center w-100 p-4"
                      style="border-radius: 10px !important;">
                      {{-- Kalau mau icon --}}
                      {{-- <i class="bi bi-box-arrow-right me-2"></i> --}}
                      <span class="text-white" style="font-size: 16px; font-weight: 600">Logout</span>
                    </button>
                  </form>
                </li>

              </ul>
            </div>

          </div>
        </div>
        <div class="section-content-right">

          <div class="header-dashboard">
            <div class="wrap">
              <div class="header-left">
                <a href="index-2.html">
                  <img class="" id="logo_header_mobile" alt=""
                    src="{{ asset('images/logo/logo.png') }}" data-light="{{ asset('images/logo/logo.png') }}"
                    data-dark="{{ asset('images/logo/logo.png') }}" data-width="154px" data-height="52px"
                    data-retina="{{ asset('images/logo/logo.png') }}">
                </a>
                <div class="button-show-hide">
                  <i class="icon-menu-left"></i>
                </div>


                <form class="form-search flex-grow">
                  <fieldset class="name">
                    <input type="text" placeholder="Search here..." class="show-search" id="search-input"
                      name="name" tabindex="2" value="" aria-required="true" required=""
                      autocomplete="off">
                  </fieldset>
                  <div class="button-submit">
                    <button class="" type="submit"><i class="icon-search"></i></button>
                  </div>
                  <div class="box-content-search">
                    <ul id="box-content-search"></ul>
                  </div>
                </form>

              </div>
              <div class="header-grid">

                <div class="popup-wrap user type-header">
                  <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton3"
                      data-bs-toggle="dropdown" aria-expanded="false">
                      <span class="header-user wg-user">
                        <span class="image">
                          <img src="images/avatar/personal.png" alt="">
                        </span>
                        <span class="flex flex-column">
                          <span class="body-title mb-2">{{ Auth::user()->name }}</span>
                          <span class="text-tiny">{{ Auth::user()->role ?? 'Admin' }}</span>
                        </span>
                      </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end has-content" aria-labelledby="dropdownMenuButton3">
                      <li>
                        <a href="#" class="user-item">
                          <div class="icon">
                            <i class="icon-user"></i>
                          </div>
                          <div class="body-title-2">Account</div>
                        </a>
                      </li>

                      <li class="menu-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                          @csrf
                          <button type="submit" class="user-item d-flex align-items-center w-100 p-4 btn btn-danger"
                            style="border-radius: 10px !important;">
                            <div class="body-title-2 text-white">Log out</div>
                          </button>
                        </form>
                      </li>

                    </ul>
                  </div>
                </div>

              </div>
            </div>
          </div>
          <div class="main-content">

            @yield('content')

            <div class="bottom-page">
              <div class="body-text">Copyright © 2025 UMKM Wakatobi</div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
  <script src="{{ asset('js/sweetalert.min.js') }}"></script>
  <script src="{{ asset('js/apexcharts/apexcharts.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>
  {{-- search --}}
  <script>
    $(function() {
      $("#search-input").on("keyup", function() {
        var searchQuery = $(this).val();
        if (searchQuery.length > 2) {
          $.ajax({
            type: "GET",
            url: "{{ route('admin.search') }}",
            data: {
              query: searchQuery
            },
            dataType: 'json',
            success: function(data) {
              $("#box-content-search").html('');
              $.each(data, function(index, item) {
                var url = "{{ route('admin.product.edit', ['id' => 'product_id']) }}";
                var link = url.replace('product_id', item.id);

                $("#box-content-search").append(`
                                <li>
                                    <ul>
                                        <li class="product-item gap14 mb-10">
                                            <div class="image no-bg">
                                                <img src="{{ asset('uploads/products/thumbnails') }}/${item.image}" alt="${item.name}">
                                            </div>
                                            <div class="flex items-center justify-between gap20 flex-grow">
                                                <div class="name">
                                                    <a href="${link}" class="body-text">${item.name}</a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-10">
                                            <div class="divider"></div>
                                        </li>
                                    </ul>
                                </li>
                                `);
              });
            }

          });
        }
      });
    });
  </script>
  {{-- endsearch --}}
  <script>
    (function($) {

      var tfLineChart = (function() {

        var chartBar = function() {

          var options = {
            series: [{
                name: 'Total',
                data: [0.00, 0.00, 0.00, 0.00, 0.00, 273.22, 208.12, 0.00, 0.00,
                  0.00, 0.00, 0.00
                ]
              }, {
                name: 'Pending',
                data: [0.00, 0.00, 0.00, 0.00, 0.00, 273.22, 208.12, 0.00, 0.00,
                  0.00, 0.00, 0.00
                ]
              },
              {
                name: 'Delivered',
                data: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00,
                  0.00, 0.00
                ]
              }, {
                name: 'Canceled',
                data: [0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00,
                  0.00, 0.00
                ]
              }
            ],
            chart: {
              type: 'bar',
              height: 325,
              toolbar: {
                show: false,
              },
            },
            plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: '10px',
                endingShape: 'rounded'
              },
            },
            dataLabels: {
              enabled: false
            },
            legend: {
              show: false,
            },
            colors: ['#2377FC', '#FFA500', '#078407', '#FF0000'],
            stroke: {
              show: false,
            },
            xaxis: {
              labels: {
                style: {
                  colors: '#212529',
                },
              },
              categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
                'Oct', 'Nov', 'Dec'
              ],
            },
            yaxis: {
              show: false,
            },
            fill: {
              opacity: 1
            },
            tooltip: {
              y: {
                formatter: function(val) {
                  return "$ " + val + ""
                }
              }
            }
          };

          chart = new ApexCharts(
            document.querySelector("#line-chart-8"),
            options
          );
          if ($("#line-chart-8").length > 0) {
            chart.render();
          }
        };

        /* Function ============ */
        return {
          init: function() {},

          load: function() {
            chartBar();
          },
          resize: function() {},
        };
      })();

      jQuery(document).ready(function() {});

      jQuery(window).on("load", function() {
        tfLineChart.load();
      });

      jQuery(window).on("resize", function() {});
    })(jQuery);
  </script>

  @stack('scripts')
</body>

</html>
