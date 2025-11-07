<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Muestra la lista de productos con filtros dinámicos.
     * Ejemplo:
     * GET /api/products?filter[name]=laptop&included=category,supplier&sort=-price&perPage=10
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->included()      // Relaciones dinámicas (?included=category,supplier)
            ->filter()        // Filtros dinámicos (?filter[name]=Laptop)
            ->sort()          // Orden dinámico (?sort=-price)
            ->getOrPaginate();// Paginación o todos (?perPage=10)

        return response()->json([
            'success' => true,
            'data' => $products
        ], Response::HTTP_OK);
    }

    /**
     * Guarda un nuevo producto.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'category_id'  => 'required|exists:categories,id',
            'supplier_id'  => 'required|exists:suppliers,id',
        ]);

        $product = Product::create($request->only(['name', 'price', 'category_id', 'supplier_id']));

        return response()->json([
            'success' => true,
            'message' => 'Producto creado correctamente.',
            'data' => $product
        ], Response::HTTP_CREATED);
    }

    /**
     * Muestra un producto específico (con include opcional).
     * Ejemplo:
     * GET /api/products/1?included=category,supplier
     */
    public function show($id)
    {
        $product = Product::query()
            ->included()
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Actualiza un producto existente.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'         => 'sometimes|required|string|max:255',
            'price'        => 'sometimes|required|numeric|min:0',
            'category_id'  => 'sometimes|required|exists:categories,id',
            'supplier_id'  => 'sometimes|required|exists:suppliers,id',
        ]);

        $product->update($request->only(['name', 'price', 'category_id', 'supplier_id']));

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado correctamente.',
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Elimina un producto.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente.'
        ], Response::HTTP_OK);
    }
}
