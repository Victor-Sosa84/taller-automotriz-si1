<?php
namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Herramienta;
use App\Models\ManoObra;
use App\Models\MarcaHerramienta;
use App\Models\Repuesto;
use App\Models\TipoHerramienta;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function taller()
    {
        $repuestos    = Repuesto::orderBy('id')->get();
        $servicios    = ManoObra::orderBy('id')->get();
        $herramientas = Herramienta::with(['tipo', 'marca'])->orderBy('nro')->get();
        $tipos        = TipoHerramienta::orderBy('id')->get();
        $marcas       = MarcaHerramienta::orderBy('id')->get();
        return view('catalogo.taller', compact('repuestos', 'servicios', 'herramientas', 'tipos', 'marcas'));
    }

    public function buscarCatalogo(?string $tipo = null, ?string $nombre = null)
    {
        $resultado = [];

        if (!$tipo || $tipo === 'repuesto') {
            $query = Repuesto::query();
            if ($nombre) {
                $query->where('nombre', 'like', '%' . $nombre . '%');
            }
            $resultado['repuestos'] = $query->orderBy('nombre')->limit(50)->get(['id', 'nombre', 'marca', 'estado', 'precio_referencial']);
        }

        if (!$tipo || $tipo === 'mano_obra') {
            $query = ManoObra::query();
            if ($nombre) {
                $query->where('descripcion', 'like', '%' . $nombre . '%');
            }
            $resultado['mano_obra'] = $query->orderBy('descripcion')->limit(50)->get(['id', 'descripcion', 'costo_referencial']);
        }

        if (!$tipo || $tipo === 'herramienta') {
            $query = Herramienta::with(['tipo', 'marca']);
            if ($nombre) {
                $query->where('descripcion', 'like', '%' . $nombre . '%');
            }
            $resultado['herramientas'] = $query->orderBy('nro')->limit(50)->get()->map(fn ($h) => [
                'nro'         => $h->nro,
                'descripcion' => $h->descripcion,
                'tipo'        => $h->tipo?->descripcion,
                'marca'       => $h->marca?->nombre,
                'estado'      => $h->estado,
                'disponible'  => $h->disponible,
            ]);
        }

        return $resultado;
    }

    // Repuesto
    public function storeRepuesto(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100', 'marca' => 'nullable|string|max:50', 'estado' => 'nullable|string|max:50', 'precio_referencial' => 'nullable|numeric|min:0']);
        Repuesto::create($request->only('nombre', 'marca', 'estado', 'precio_referencial'));
        Bitacora::registrar('Crear Repuesto', "Repuesto: {$request->nombre}");
        return back()->with('success', 'Repuesto agregado.');
    }

    public function updateRepuesto(Request $request, int $id)
    {
        $request->validate(['nombre' => 'required|string|max:100', 'marca' => 'nullable|string|max:50', 'estado' => 'nullable|string|max:50']);
        Repuesto::findOrFail($id)->update($request->only('nombre', 'marca', 'estado', 'precio_referencial'));
        Bitacora::registrar('Editar Repuesto', "Repuesto #{$id}");
        return back()->with('success', 'Repuesto actualizado.');
    }

    public function destroyRepuesto(int $id)
    {
        Repuesto::findOrFail($id)->delete();
        Bitacora::registrar('Eliminar Repuesto', "Repuesto #{$id}");
        return back()->with('success', 'Repuesto eliminado.');
    }

    // Mano de Obra
    public function storeManoObra(Request $request)
    {
        $request->validate(['descripcion' => 'required|string|max:255', 'costo_referencial' => 'nullable|numeric|min:0']);
        ManoObra::create($request->only('descripcion', 'costo_referencial'));
        Bitacora::registrar('Crear Mano de Obra', "MO: {$request->descripcion}");
        return back()->with('success', 'Mano de obra agregada.');
    }

    public function updateManoObra(Request $request, int $id)
    {
        $request->validate(['descripcion' => 'required|string|max:255']);
        ManoObra::findOrFail($id)->update($request->only('descripcion', 'costo_referencial'));
        Bitacora::registrar('Editar Mano de Obra', "MO #{$id}");
        return back()->with('success', 'Mano de obra actualizada.');
    }

    public function destroyManoObra(int $id)
    {
        ManoObra::findOrFail($id)->delete();
        Bitacora::registrar('Eliminar Mano de Obra', "MO #{$id}");
        return back()->with('success', 'Mano de obra eliminada.');
    }

    // Herramienta
    public function storeHerramienta(Request $request)
    {
        $request->validate(['descripcion' => 'nullable|string|max:150', 'id_tipo_herramienta' => 'required|integer', 'id_marca_herramienta' => 'required|integer', 'estado' => 'nullable|string|max:50']);
        Herramienta::create($request->only('descripcion', 'id_tipo_herramienta', 'id_marca_herramienta', 'estado') + ['disponible' => true]);
        Bitacora::registrar('Crear Herramienta', "Herramienta: {$request->descripcion}");
        return back()->with('success', 'Herramienta agregada.');
    }

    public function updateHerramienta(Request $request, int $nro)
    {
        $request->validate(['descripcion' => 'nullable|string|max:150', 'estado' => 'nullable|string|max:50']);
        Herramienta::findOrFail($nro)->update($request->only('descripcion', 'estado', 'id_tipo_herramienta', 'id_marca_herramienta'));
        Bitacora::registrar('Editar Herramienta', "Herramienta #{$nro}");
        return back()->with('success', 'Herramienta actualizada.');
    }

    public function destroyHerramienta(int $nro)
    {
        Herramienta::findOrFail($nro)->delete();
        Bitacora::registrar('Eliminar Herramienta', "Herramienta #{$nro}");
        return back()->with('success', 'Herramienta eliminada.');
    }

    // Tipo Herramienta
    public function storeTipo(Request $request)
    {
        $request->validate(['descripcion' => 'required|string|max:100']);
        TipoHerramienta::create($request->only('descripcion'));
        Bitacora::registrar('Crear Tipo Herramienta', $request->descripcion);
        return back()->with('success', 'Tipo agregado.');
    }

    public function updateTipo(Request $request, int $id)
    {
        $request->validate(['descripcion' => 'required|string|max:100']);
        TipoHerramienta::findOrFail($id)->update($request->only('descripcion'));
        Bitacora::registrar('Editar Tipo Herramienta', "Tipo #{$id}");
        return back()->with('success', 'Tipo actualizado.');
    }

    public function destroyTipo(int $id)
    {
        TipoHerramienta::findOrFail($id)->delete();
        Bitacora::registrar('Eliminar Tipo Herramienta', "Tipo #{$id}");
        return back()->with('success', 'Tipo eliminado.');
    }

    // Marca Herramienta
    public function storeMarca(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:50']);
        MarcaHerramienta::create($request->only('nombre'));
        Bitacora::registrar('Crear Marca Herramienta', $request->nombre);
        return back()->with('success', 'Marca agregada.');
    }

    public function updateMarca(Request $request, int $id)
    {
        $request->validate(['nombre' => 'required|string|max:50']);
        MarcaHerramienta::findOrFail($id)->update($request->only('nombre'));
        Bitacora::registrar('Editar Marca Herramienta', "Marca #{$id}");
        return back()->with('success', 'Marca actualizada.');
    }

    public function destroyMarca(int $id)
    {
        MarcaHerramienta::findOrFail($id)->delete();
        Bitacora::registrar('Eliminar Marca Herramienta', "Marca #{$id}");
        return back()->with('success', 'Marca eliminada.');
    }
}