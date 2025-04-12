<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::pattern('id', '[0-9]+'); // artinya ketika ada parameter {id}, maka harus berupa angka
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register.store');
Route::middleware(['auth'])->group(function () { // artinya semua route di dalam group ini harus login dulu
    // masukkan semua route yang perlu autentikasi di sini
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/', [WelcomeController::class, 'index']);
    Route::group(['prefix' => 'user'], function () {
        Route::middleware('authorize:ADM,')->group(function () {
            Route::get('/', [UserController::class, 'index']);          // menampilkan halaman awal user
            Route::post('/list', [UserController::class, 'list']);      // menampilkan data user dalam bentuk json untuk datatables
            Route::get('/create', [UserController::class, 'create']);   // menampilkan halaman form tambah user 
            Route::post('/ajax', [UserController::class, 'store_ajax']);        // Menyimpan data user baru Ajax
            Route::get('/create_ajax', [UserController::class, 'create_ajax']); // Menampilkan halaman form tambah user
            Route::post('/', [UserController::class, 'store']);         // menyimpan data user baru
            Route::get('/{id}', [UserController::class, 'show']);       // menampilkan detail user
            Route::get('/{id}/edit', [UserController::class, 'edit']);  // menampilkan  halaman form edit user
            Route::put('/{id}', [UserController::class, 'update']);     // menyimpan perubahan data user 
            Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax']); //Menampilkan halaman form show user ajax
            Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);    //Menampilkan halaman form edit user Ajax
            Route::put('{id}/update_ajax', [UserController::class, 'update_ajax']); //Menyimpan perubahan data user Ajax 
            Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']); // Untuk tampilkan form confirm delete user Ajax
            Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']); // Untuk hapus data user Ajax 
            Route::delete('/{id}', [UserController::class, 'destroy']); // menghapus data user
            Route::get('/import', [UserController::class, 'import']);              // ajax form upload excel
            Route::post('/import_ajax', [UserController::class, 'import_ajax']);   // ajax import excel
            Route::get('/export_excel', [UserController::class, 'export_excel']);  // export excel
        });
    });


    Route::group(['prefix' => 'level'], function () {
        Route::middleware('authorize:ADM')->group(function () {
            Route::get('/', [LevelController::class, 'index']);                             // menampilkan halaman awal Level
            Route::post('/list', [LevelController::class, 'list']);                         // menampilkan data Level dalam bentuk json untuk datatables
            Route::get('/create', [LevelController::class, 'create']);                      // menampilkan halaman form tambah Level
            Route::post('/ajax', [LevelController::class, 'store_ajax']);                   // Menyimpan data level baru Ajax
            Route::get('/create_ajax', [LevelController::class, 'create_ajax']);            // Menampilkan halaman form tambah level
            Route::post('/', [LevelController::class, 'store']);                            // menyimpan data Level baru
            Route::get('/{id}', [LevelController::class, 'show']);                          // menampilkan detail Level
            Route::get('/{id}/edit', [LevelController::class, 'edit']);                     // menampilkan  halaman form edit Level
            Route::put('/{id}', [LevelController::class, 'update']);                        // menyimpan perubahan data Level
            Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']);           //Menampilkan halaman form show level ajax
            Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);           //Menampilkan halaman form edit level Ajax
            Route::put('{id}/update_ajax', [LevelController::class, 'update_ajax']);        //Menyimpan perubahan data level Ajax 
            Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);      // Untuk tampilkan form confirm delete level Ajax
            Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);    // Untuk hapus data level Ajax 
            Route::delete('/{id}', [LevelController::class, 'destroy']);                    // menghapus data level
            Route::get('/import', [LevelController::class, 'import']);              // ajax form upload excel
            Route::post('/import_ajax', [LevelController::class, 'import_ajax']);   // ajax import excel
            Route::get('/export_excel', [LevelController::class, 'export_excel']);  // export excel
        });
    });

    Route::group(['prefix' => 'kategori'], function () {
        Route::middleware('authorize:ADM,MNG')->group(function () {
            Route::get('/', [KategoriController::class, 'index']);                          // menampilkan halaman awal kategori
            Route::post('/list', [KategoriController::class, 'list']);                      // menampilkan data kategori dalam bentuk json untuk datatables
            Route::get('/create', [KategoriController::class, 'create']);                   // menampilkan halaman form tambah kategori
            Route::post('/ajax', [KategoriController::class, 'store_ajax']);                // Menyimpan data user baru Ajax
            Route::get('/create_ajax', [KategoriController::class, 'create_ajax']);         // Menampilkan halaman form tambah user
            Route::post('/', [KategoriController::class, 'store']);                         // menyimpan data kategori baru
            Route::get('/{id}', [KategoriController::class, 'show']);                       // menampilkan detail kategori
            Route::get('/{id}/edit', [KategoriController::class, 'edit']);                  // menampilkan  halaman form edit kategori
            Route::put('/{id}', [KategoriController::class, 'update']);                     // menyimpan perubahan data kategori
            Route::get('/{id}/show_ajax', [KategoriController::class, 'show_ajax']);        //Menampilkan halaman form show Kategori ajax
            Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']);        //Menampilkan halaman form edit Kategori Ajax
            Route::put('{id}/update_ajax', [KategoriController::class, 'update_ajax']);     //Menyimpan perubahan data Kategori Ajax 
            Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']);   // Untuk tampilkan form confirm delete Kategori Ajax
            Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']); // Untuk hapus data Kategori Ajax 
            Route::delete('/{id}', [KategoriController::class, 'destroy']);                 // menghapus data Kategori
            Route::get('/import', [KategoriController::class, 'import']);              // ajax form upload excel
            Route::post('/import_ajax', [KategoriController::class, 'import_ajax']);   // ajax import excel
            Route::get('/export_excel', [KategoriController::class, 'export_excel']);  // export excel
        });
    });

    Route::group(['prefix' => 'supplier'], function () {
        Route::middleware('authorize:ADM,MNG')->group(function () {
            Route::get('/', [SupplierController::class, 'index']);                          // menampilkan halaman awal kategori
            Route::post('/list', [SupplierController::class, 'list']);                      // menampilkan data kategori dalam bentuk json untuk datatables
            Route::get('/create', [SupplierController::class, 'create']);                   // menampilkan halaman form tambah kategori
            Route::post('/ajax', [SupplierController::class, 'store_ajax']);                // Menyimpan data user baru Ajax
            Route::get('/create_ajax', [SupplierController::class, 'create_ajax']);         // Menampilkan halaman form tambah user
            Route::post('/', [SupplierController::class, 'store']);                         // menyimpan data kategori baru
            Route::get('/{id}', [SupplierController::class, 'show']);                       // menampilkan detail kategori
            Route::get('/{id}/edit', [SupplierController::class, 'edit']);                  // menampilkan  halaman form edit kategori
            Route::put('/{id}', [SupplierController::class, 'update']);                     // menyimpan perubahan data kategori
            Route::get('/{id}/show_ajax', [SupplierController::class, 'show_ajax']);        //Menampilkan halaman form show Supplier ajax
            Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']);        //Menampilkan halaman form edit Supplier Ajax
            Route::put('{id}/update_ajax', [SupplierController::class, 'update_ajax']);     //Menyimpan perubahan data Supplier Ajax 
            Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']);   // Untuk tampilkan form confirm delete Supplier Ajax
            Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']); // Untuk hapus data Kategori Ajax 
            Route::delete('/{id}', [SupplierController::class, 'destroy']);                 // menghapus data user
            Route::get('/import', [SupplierController::class, 'import']);              // ajax form upload excel
            Route::post('/import_ajax', [SupplierController::class, 'import_ajax']);   // ajax import excel
            Route::get('/export_excel', [SupplierController::class, 'export_excel']);  // export excel
        });
    });

    Route::group(['prefix' => 'barang'], function () {
        Route::middleware('authorize:ADM,MNG,STF')->group(function () {
            Route::get('/', [BarangController::class, 'index']);                            // menampilkan halaman awal barang
            Route::post('/list', [BarangController::class, 'list']);                        // menampilkan data barang dalam bentuk json untuk datatables
            Route::get('/create', [BarangController::class, 'create']);                     // menampilkan halaman form tambah barang
            Route::post('/ajax', [BarangController::class, 'store_ajax']);                  // Menyimpan data user baru Ajax
            Route::get('/create_ajax', [BarangController::class, 'create_ajax']);           // Menampilkan halaman form tambah user
            Route::post('/', [BarangController::class, 'store']);                           // menyimpan data barang baru
            Route::get('/{id}', [BarangController::class, 'show']);                         // menampilkan detail barang
            Route::get('/{id}/edit', [BarangController::class, 'edit']);                    // menampilkan  halaman form edit barang
            Route::put('/{id}', [BarangController::class, 'update']);                       // menyimpan perubahan data barang
            Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']);          //Menampilkan halaman form show Barang ajax
            Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);          //Menampilkan halaman form edit Barang Ajax
            Route::put('{id}/update_ajax', [BarangController::class, 'update_ajax']);       //Menyimpan perubahan data Barang Ajax 
            Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);     // Untuk tampilkan form confirm delete Barang Ajax
            Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);   // Untuk hapus data Barang Ajax 
            Route::delete('/{id}', [BarangController::class, 'destroy']);                   // menghapus data barang
            Route::get('/import', [BarangController::class, 'import']);              // ajax form upload excel
            Route::post('/import_ajax', [BarangController::class, 'import_ajax']);   // ajax import excel
            Route::get('/export_excel', [BarangController::class, 'export_excel']);  // export excel
        });
    });
});
