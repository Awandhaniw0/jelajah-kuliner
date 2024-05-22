@extends('layouts.layout2')

@section('title')
    DETIL PESANAN!
@endsection

@section('css')
    <link rel="stylesheet" href="/css/pesan.css">
@endsection

@section('main')
    <div class="all">
        <div class="up border border-bottom d-flex justify-content-between align-items-center">
            <a href="/dashboard"><button class="btn btn-danger">Back to Dashboard</button></a>
            <p class="namaakun m-0">Mau ngerjakan orderan siapa, {{ session('account')['nama'] }}? 🤔</p>
        </div>
        <div class="nmpkl">
            @foreach ($produks as $p)
                @php
                    $produk = App\Models\Produk::where('id', $p->idProduk)->first();
                    $pkl = App\Models\PKL::where('id', $produk->idPKL)->first();
                @endphp
            @endforeach
        </div>

        <div class="showmenu" style="padding-top: 5px; padding-bottom: 5px">
            <div class="kiri border border-right" style="width: 100%;">
                <h3 class="namap" style="border-bottom: 1px solid #ccc;"><strong>{{ $pkl->namaPKL }}</strong></h3>
                <p class="deskri">{{ $pkl->desc }}</p>
                @if (count($produks) > 0)
                    @foreach ($produks as $p)
                        @php
                            $produk = App\Models\Produk::where('id', $p->idProduk)->first();
                            $jmlhtotal = 0;
                        @endphp
                        @if ( $p->JumlahProduk != 0)

                        <div class="card">
                            <div class="inCard" id="theImage">
                                <img src="https://i.pinimg.com/564x/34/e1/30/34e13046e8f9fd9f3360568abd453685.jpg"
                                alt="">

                                {{-- <img src="{{ $p->image_url }}" alt="" width="100px"> --}}
                            </div>
                            <div class="inCard" id="mid">
                                <p class="np">{{ $produk->namaProduk }}</p>
                                <p class="Des">{{ $produk->namaProduk }}</p>
                                <p class="hrg">Rp. {{ $produk->harga }}</p>
                            </div>
                            <div class="inCard" id="leftt">
                                <p>Quantity: {{ $p->JumlahProduk }}</p>
                            </div>
                        </div>
                        @endif
                    @endforeach
                @else
                    <p class="namap" style="text-align: center">Produk Kosong</p>
                @endif
                @if ($pesan->status == 'Pesanan Baru' && @session('account')['status'] == "PKL")
                    <br>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-danger me-2" onclick="confirmTolakPesanan('{{ $pesan->id }}')">
                            Tolak Pesanan
                        </button>
                        <button class="btn btn-success" onclick="confirmTerimaPesanan('{{ $pesan->id }}')">
                            Terima Pesanan
                        </button>
                    </div>
                    <br>
                @endif
                @if ($pesan->status == 'Pesanan Baru' && $pesan->idAccount == session('account')['id'])
                <br>
                @endif
                @php
                    $pkl = \App\Models\PKL::where('idAccount', session('account')['id'])->first();
                @endphp
                @if ($pkl)
                @if ($pesan->status == 'Pesanan Diproses' && @$pesan->idPKL == $pkl->id)
                <br>
                <button class="btn btn-success" onclick="selesaiPesanan('{{ @$pesan->id }}')">
                    Pesanan Selesai
                </button>
                <br>
                @endif
                @endif
            </div>

            <div class="kanan border border-right d-flex flex-column justify-content-between"
                style="height: 100%; width: 50%; margin-left: 5px;">
                <p style="margin-top: 1vh; margin-bottom: 1vh; text-align: center;"><strong>(👉ﾟヮﾟ)👉  {{ now()->format('l, d F Y') }}  👈(ﾟヮﾟ👈)</strong></p>
                <table id="tabelStruk" class="table" style="position: absolute; width: 33%; margin-top: 5vh">
                    <thead>
                        <tr>
                            <th>ID PRODUK</th>
                            <th>Nama Produk</th>
                            <th>Quantity</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        @foreach ($produks as $p)
                            @php
                                $produk = App\Models\Produk::where('id', $p->idProduk)->first();
                            @endphp
                            <tr>
                                @if ($p->JumlahProduk   != 0)

                                <td>{{ $p->idProduk }}</td>
                                <td>{{ $produk->namaProduk }}</td>
                                <td>{{ $p->JumlahProduk }}</td>
                                <td>{{ $p->JumlahProduk * $produk->harga }}</td>
                                @endif
                            </tr>
                            @php
                                $jmlhtotal += $p->JumlahProduk;
                            @endphp
                        @endforeach
                        <tr>
                            <td colspan="3">Total Quantity</td>
                            <td id="totalQuantity">{{ $jmlhtotal }}</td>
                        </tr>
                        <tr>
                            <td colspan="3">Total Keseluruhan</td>
                            <td id="totalKeseluruhan">{{ $pesan->TotalBayar }}</td>
                        </tr>
                    </tfoot>
                </table>
                <div style="margin-top: 30vh">
                    <label for="keterangan">Status Pesanan</label><br>
                    <input type="text" name="keterangan" id="keterangan" value="{{ $pesan->status }}" readonly>
                </div>
                <div style="margin-bottom: 20vh">
                    <label for="keterangan">Keterangan Tambahan (Opsional):</label><br>
                    <input type="text" name="keterangan" id="keterangan" placeholder="Contoh: Tidak pedas ya mas!" value="-" style="width: 80%; height: 5vh;">
                </div>
                @if ($pesan->status == 'Pesanan Baru')

                <button class="btn btn-danger" style   ="width: 40%; margin-left: auto; margin-right: auto;" onclick="confirmBatalPesanan('{{ $pesan->id }}')">Batalkan Pesanan!</button>
                @endif
            </div>
        </div>
    </div>

    <script>
        function confirmTerimaPesanan(id) {
            if (confirm("Apakah kamu yakin untuk menerima pesanan ini?")) {
                window.location.href = "/terimaPesanan/" + id;
            }
        }

        function selesaiPesanan(id) {
            if (confirm("Apakah kamu yakin Pesanan Sudah Selesai?")) {
                window.location.href = "/selesaiPesanan/" + id;
            }
        }

        function confirmTolakPesanan(id) {
            if (confirm("Apakah kamu yakin untuk menolak pesanan ini?")) {
                window.location.href = "/tolakPesanan/" + id;
            }
        }

        function confirmBatalPesanan(id) {
            if (confirm("Apakah kamu yakin untuk Membatalkan pesanan ini?")) {
                window.location.href = "/batalPesanan/" + id;
            }
        }
    </script>
@endsection
