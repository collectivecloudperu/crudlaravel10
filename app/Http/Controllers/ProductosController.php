<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Productos;
use Session;
use Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItemCreateRequest;
use App\Http\Requests\ItemUpdateRequest;
use Illuminate\Support\Facades\Validator;
use DB;
use Input;
use Storage;
use DateTime; 

class ProductosController extends Controller
{
    
    // Listar todos los productos en la vista principal 
    public function index()
    {
        $productos = Productos::all();
        return view('admin.productos.index', compact('productos')); 
    }

    // Crear un Registro (Create) 
    public function crear()
    {
        $productos = Productos::all();
        return view('admin.productos.crear', compact('productos'));
    } 

    // Proceso de Creación de un Registro 
    public function store(ItemCreateRequest $request)
    {
        // Instancio al modelo Productos que hace llamado a la tabla 'productos' de la Base de Datos
        $productos = new Productos; 

        // Recibo todos los datos del formulario de la vista 'crear.blade.php'
        $productos->nombre = $request->nombre;
        $productos->precio = $request->precio;
        $productos->stock = $request->stock;
        
        // Almacenos la imagen en la carpeta publica especifica, esto lo veremos más adelante 
        $productos->img = $request->file('img')->store('/');
        
        // Guardamos la fecha de creación del registro 
        $productos->created_at = (new DateTime)->getTimestamp();

        // Inserto todos los datos en mi tabla 'productos' 
        $productos->save();

        // Hago una redirección a la vista principal con un mensaje 
        return redirect('admin/productos')->with('message','Guardado Satisfactoriamente !'); 
    } 

    // Leer Registro por 'id' (Read) 
    public function show($id)
    {
        $productos = Productos::find($id);
        return view('admin.productos.detalles', compact('productos')); 
    } 

    //  Actualizar un registro (Update)
    public function actualizar($id)
    {
        $productos = Productos::find($id);
        return view('admin/productos.actualizar',['productos'=>$productos]);
    } 

    
    // Proceso de Actualización de un Registro (Update)
    public function update(ItemUpdateRequest $request, $id)
    {        
        // Recibo todos los datos desde el formulario Actualizar
        $productos = Productos::find($id);
        $productos->nombre = $request->nombre;
        $productos->precio = $request->precio;
        $productos->stock = $request->stock;

        // Recibo la imagen desde el formulario Actualizar
        if ($request->hasFile('img')) {
            $productos->img = $request->file('img')->store('/');
        }

        // Guardamos la fecha de actualización del registro 
        $productos->updated_at = (new DateTime)->getTimestamp();
        
        // Actualizo los datos en la tabla 'productos'
        $productos->save();

        // Muestro un mensaje y redirecciono a la vista principal 
        Session::flash('message', 'Editado Satisfactoriamente !');
        return Redirect::to('admin/productos');
    }

    // Eliminar un Registro 
    public function eliminar($id)
    {
        // Indicamos el 'id' del registro que se va Eliminar
        $productos = Productos::find($id);

        // Elimino la imagen de la carpeta 'uploads', esto lo veremos más adelante
        $imagen = explode(",", $productos->img);
        Storage::delete($imagen);
            
        // Elimino el registro de la tabla 'productos' 
        Productos::destroy($id); 

        // Opcional: Si deseas guardar la fecha de eliminación de un registro, debes mantenerlo en 
        // una tabla llamada por ejemplo 'productos_eliminados' y alli guardas su fecha de eliminación 
        // $productos->deleted_at = (new DateTime)->getTimestamp();
            
        // Muestro un mensaje y redirecciono a la vista principal 
        Session::flash('message', 'Eliminado Satisfactoriamente !');
        return Redirect::to('admin/productos');
    } 

} 


