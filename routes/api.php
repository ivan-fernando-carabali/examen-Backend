<?php

use App\Http\Controllers\Api\ProductController as ApiProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\PaymentController;

    Route::apiResource('categories', CategoryController::class);

    // Warehouses
    Route::apiResource('warehouses', WarehouseController::class);

    // Clients
    Route::apiResource('clients', ClientController::class);

    // Suppliers
    Route::apiResource('suppliers', SupplierController::class);

    // Products
  Route::apiResource('products',ProductController::class);

    // Inventory
    Route::apiResource('inventories', InventoryController::class);

    // Orders
    Route::apiResource('orders', OrderController::class);

    // Order Details
    Route::apiResource('order-details', OrderDetailController::class);

    // Invoices
    Route::apiResource('invoices', InvoiceController::class);

    // Invoice Details
    Route::apiResource('invoice-details', InvoiceDetailController::class);

    // Payments
    Route::apiResource('payments', PaymentController::class);

