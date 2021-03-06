<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Admin\Permission;
use App\Profile;
use DB;
use Exception;

use App\Http\Requests\Admin\PermissionRequest as ModelRequest;
use App\Http\Requests\Admin\DeleteRequest as DeleteRequest;
use App\Http\Requests\Admin\PermissionSearchRequest as SearchRequest;
use App\Models\Admin\Role;


class PermissionsController extends Controller {


    protected $model_name = 'Permission';
    protected $index_view = 'admin.permissions.index';
    protected $create_view = 'admin.permissions.create';
    protected $show_view = 'admin.permissions.show';
    protected $edit_view = 'admin.permissions.edit';

    protected $index_route = 'admin.permissions.index';
    protected $create_route = 'admin.permissions.create';
    protected $show_route = 'admin.permissions.show';
    protected $edit_route = 'admin.permissions.edit';
    protected $trash_route = 'admin.permissions.trash';


    protected $sort_fields = ['id', 'name', 'display_name'];
    protected $filter_fields = ['id', 'name', 'display_name','description','roles'];

    public function __construct()
    {
        $this->middleware('admin');
    }


    public function show_trash()
    {
        return Session($this->index_view . '.trash', false);
    }

    public function trash($value = false)
    {
        if (isset($value))
        {
            if ($value)
            {
                $value = true;
            }
            else
            {
                $value = false;
            }
        } else
        {
            $value = false;
        }
        Session( [ $this->index_view.'.trash' => $value] );
        return redirect(route($this->index_route));
    }

    public function filter(SearchRequest $request)
    {
        foreach ($this->filter_fields as $field) {
            Profile::loginProfile()->setFilterValue($this->index_view, $field, $request->input($field, ''));
        }
        return redirect(route($this->index_route));
    }

    public function sort($column = null, $order = null)
    {
        if (isset($order)) {
            if ($order !== 'desc') {
                $order = 'asc';
            }
        } else {
            $order = 'asc';
        };

        if (isset($column)) {
            if (in_array($column, $this->sort_fields)) {
                Profile::loginProfile()->setOrderByValue($this->index_view, $column, $order);
            }
        } else {
            Profile::loginProfile()->setOrderBy($this->index_view, []);
        };

        return redirect(route($this->index_route));
    }


    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $filter = $this->getFilter();
        $models = $this->getModels($filter)->with('roles');
        $models = $models->paginate(Profile::loginProfile()->per_page);
        return view($this->index_view, compact('models', 'filter'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $model = new Permission();
        $roles = Role::lists('acronym', 'id');
        $model_roles = [];
        return view($this->create_view, compact(['model','roles','model_roles']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $roles = Role::all()->lists('acronym','id');
            $model = $this->getModel($id);
            $model_roles = $model->roles->lists('id');
            return view($this->show_view, compact('model','roles','model_roles'));
        } catch (Exception $e) {
            flash()->warning("$this->model_name $id not found");
            return $this->index();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $roles = Role::all()->lists('acronym','id');
        $model = $this->getModel($id);
        $model_roles = $model->roles->lists('id');
        return view($this->edit_view, compact(['model','roles','model_roles']));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(ModelRequest $request)
    {
        try {
            $roles = $request->input('roles', []);
            $model = new Permission($request->all());
            try {
                DB::beginTransaction();
                $model->save();
                $model->roles()->sync($roles);
                DB::commit();
                flash()->info("$this->model_name saved");
                return redirect(route($this->show_route, [$model->id]));
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            $errors = [];
            flash()->error($e->getMessage());
            return $request->response($errors);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id, ModelRequest $request)
    {
        try {
            $roles = $request->input('roles', []);
            $model = $this->getModel($id);
            try {
                DB::beginTransaction();
                $model->update($request->all());
                $model->roles()->sync($roles);
                DB::commit();
                flash()->info("$this->model_name saved");
                return redirect(route($this->show_route, [$model->id]));
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            $errors = [];
            flash()->error($e->getMessage());
            return $request->response($errors);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id, DeleteRequest $request)
    {
        try {
            $model = $this->getModel($id);
            $model->delete();
            flash()->info("$this->model_name sent to trash");
            if ($this->show_trash()) {
                return redirect(route($this->show_route, [$id]));
            } else {
                return redirect(route($this->index_route));
            }
        } catch (Exception $e) {
            flash()->error($e->getMessage());
            return $request->response([]);
        }
    }


    public function restore($id, DeleteRequest $request)
    {
        try {
            $model = $this->getModel($id);
            $model->restore();
            flash()->info("$this->model_name restored");
            return redirect(route($this->show_route, [$id]));
        } catch (Exception $e) {
            flash()->error($e->getMessage());
            return $request->response([]);
        }
    }

    public function forcedelete($id, DeleteRequest $request)
    {
        try {
            $model = $this->getModel($id);
            DB::transaction(
                function () use ($model) {
                    $model->roles()->sync([]);
                    $model->forcedelete();
                });
            flash()->info("$this->model_name removed");
            return redirect(route($this->index_route));
        } catch (Exception $e) {
            flash()->error($e->getMessage());
            return $request->response([]);
        }
    }


    public function getModels($filter = null)
    {
        $models = Permission::sortable($this->index_view);
        if ($this->show_trash()) {
            $models = $models->withTrashed();
        }
        if (isset($filter)) {
            foreach ($this->filter_fields as $field) {
                if (trim($filter[$field]) != '') {
                    $values = explode(',', $filter[$field]);
                    $first = true;
                    foreach ($values as $value) {
                        if ($field == 'id') {
                            if ($first) {
                                $models = $models->Where($field, $value);
                                $first = false;
                            } else {
                                $models = $models->orWhere($field, $value);
                            }
                        } else if ($field == 'roles') {
                            $value = '%' . $value . '%';
                            if ($first) {
                                $models = $models->whereHas('roles', function ($q) use ($value) {
                                    $q->where('acronym', 'like', $value);
                                });
                                $first = false;
                            } else {
                                $models = $models->orWhereHas('roles', function ($q) use ($value) {
                                    $q->where('acronym', 'like', $value);
                                });
                            }

                        } else {
                            $value = '%' . $value . '%';
                            if ($first) {
                                $models = $models->Where($field, 'LIKE', $value);
                                $first = false;
                            } else {
                                $models = $models->orWhere($field, 'LIKE', $value);
                            }
                        }
                    }
                }
            }
        }
        return $models;
    }

    public function getModel($id)
    {
        return $this->getModels()->findOrFail($id);
    }

    public function getFilter()
    {
        $values = [];
        foreach ($this->filter_fields as $field) {
            $values[$field] = Profile::loginProfile()->getFilterValue($this->index_view, $field);
        }
        return $values;
    }

}

