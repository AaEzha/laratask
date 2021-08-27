<?php

namespace App\Http\Controllers;

use App\Mail\NewRegistered;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use GroceryCrud\Core\GroceryCrud;
use Illuminate\Support\Facades\Mail;

class AdminController extends GroceryController
{
    public function roles()
    {
        $crud = $this->_getGroceryCrudEnterprise();

        $crud->setTable('roles');
        $crud->setSubject('Role', 'Roles');
        $crud->unsetFields(['created_at', 'updated_at']);
        $crud->unsetColumns(['created_at']);
        $crud->callbackBeforeInsert(function ($s) {
            $s->data['created_at'] = now();
            $s->data['updated_at'] = now();
            return $s;
        });
        $crud->callbackBeforeUpdate(function ($s) {
            $s->data['updated_at'] = now();
            return $s;
        });

        $output = $crud->render();

        return $this->_showOutput($output);
    }

    public function users()
    {
        $crud = $this->_getGroceryCrudEnterprise();

        $crud->setTable('users');
        $crud->setSubject('User', 'Users');
        $crud->columns(['name', 'email', 'role_id']);
        $crud->fields(['name', 'email', 'password', 'role_id']);
        $crud->requiredFields(['name', 'email', 'role_id']);
        $crud->fieldType('password', 'password');
        $crud->setRelation('role_id', 'roles', 'name');
        $crud->displayAs([
            'role_id' => 'Role'
        ]);
        $crud->callbackEditField('password', function ($fieldValue, $primaryKeyValue, $rowData) {
            return '<input name="password" type="password" class="form-control" value=""  />';
        });
        $crud->callbackBeforeInsert(function ($s) {
            $s->data['created_at'] = now();
            $s->data['updated_at'] = now();
            $s->data['password_shadow'] = $s->data['password'];
            $s->data['password'] = bcrypt($s->data['password']);
            return $s;
        });
        $crud->callbackBeforeUpdate(function ($s) {
            $s->data['updated_at'] = now();
            return $s;
        });
        $crud->callbackAfterInsert(function ($s) {
            $user = User::find($s->insertId);

            Mail::to($user->email)
                ->queue(new NewRegistered($user));

            $user->password_shadow = null;
            $user->save();

            return $s;
        });

        $output = $crud->render();

        return $this->_showOutput($output);
    }

    public function projects()
    {
        $crud = $this->_getGroceryCrudEnterprise();

        $crud->setTable('projects');
        $crud->setSubject('Project', 'Projects');
        $crud->unsetFields(['created_at', 'updated_at']);
        $crud->unsetColumns(['created_at']);
        $crud->setTexteditor(['description']);
        $crud->setActionButton('Members', 'fa fa-users', function ($row) {
            return route('admin.project_members', $row->id);
        }, false);
        $crud->setActionButton('Tasks', 'fa fa-list', function ($row) {
            return route('admin.project_tasks', $row->id);
        }, false);
        $crud->callbackBeforeInsert(function ($s) {
            $s->data['created_at'] = now();
            $s->data['updated_at'] = now();
            return $s;
        });
        $crud->callbackBeforeUpdate(function ($s) {
            $s->data['updated_at'] = now();
            return $s;
        });

        $output = $crud->render();

        return $this->_showOutput($output);
    }

    public function project_members(Project $project)
    {
        $crud = $this->_getGroceryCrudEnterprise();

        $crud->setTable('project_members');
        $crud->setSubject('Project Member', 'Project Members of ' . $project->name);
        $crud->unsetFields(['created_at', 'updated_at', 'project_id']);
        $crud->unsetColumns(['created_at', 'project_id']);
        $crud->fieldType('project_id', 'hidden');
        $crud->setRelation('user_id', 'users', 'name');
        $crud->where(['project_id' => $project->id]);
        $crud->displayAs([
            'user_id' => 'User'
        ]);
        $crud->callbackBeforeInsert(function ($s) use ($project) {
            $s->data['created_at'] = now();
            $s->data['updated_at'] = now();
            $s->data['project_id'] = $project->id;
            return $s;
        });
        $crud->callbackBeforeUpdate(function ($s) {
            $s->data['updated_at'] = now();
            return $s;
        });

        $output = $crud->render();

        return $this->_showOutput($output);
    }

    public function project_tasks(Project $project)
    {
        $crud = $this->_getGroceryCrudEnterprise();

        $crud->setTable('project_tasks');
        $crud->setSubject('Project Task', 'Project Tasks of ' . $project->name);
        $crud->unsetColumns(['created_at', 'project_id']);
        $crud->unsetAddFields(['created_at', 'updated_at', 'project_id', 'completion_time']);
        $crud->unsetEditFields(['created_at', 'updated_at', 'project_id']);
        $crud->fieldType('project_id', 'hidden');
        $crud->setRelation('user_id', 'users', 'name');
        $crud->where(['project_id' => $project->id]);
        $crud->displayAs([
            'user_id' => 'User'
        ]);
        $crud->callbackAddField('user_id', function () use ($project) {
            $form  = "<select name='user_id' class='form-control'>";
            foreach ($project->users as $user) {
                $form .= "<option value='".$user->id."'>".$user->name."</option>";
            }
            $form .= "</select>";
            return $form;
        });
        $crud->callbackEditField('user_id', function ($fieldValue) use ($project) {
            $form  = "<select name='user_id' class='form-control'>";
            foreach ($project->users as $user) {
                $stat = ($fieldValue == $user->id) ? "selected" : "";
                $form .= "<option value='".$user->id."' $stat>".$user->name."</option>";
            }
            $form .= "</select>";
            return $form;
        });
        $crud->callbackBeforeInsert(function ($s) use ($project) {
            $s->data['created_at'] = now();
            $s->data['updated_at'] = now();
            $s->data['project_id'] = $project->id;
            return $s;
        });
        $crud->callbackBeforeUpdate(function ($s) {
            $s->data['updated_at'] = now();
            return $s;
        });

        $output = $crud->render();

        return $this->_showOutput($output);
    }

    /**
     * Get everything we need in order to load Grocery CRUD
     *
     * @return GroceryCrud
     * @throws \GroceryCrud\Core\Exceptions\Exception
     */
    private function _getGroceryCrudEnterprise()
    {
        $database = $this->_getDatabaseConnection();
        $config = config('grocerycrud');

        $crud = new GroceryCrud($config, $database);
        $crud->unsetSettings();

        return $crud;
    }

    /**
     * Grocery CRUD Output
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    private function _showOutput($output)
    {
        if ($output->isJSONResponse) {
            return response($output->output, 200)
                ->header('Content-Type', 'application/json')
                ->header('charset', 'utf-8');
        }

        $css_files = $output->css_files;
        $js_files = $output->js_files;
        $output = $output->output;

        return view('grocery', [
            'output' => $output,
            'css_files' => $css_files,
            'js_files' => $js_files
        ]);
    }

    /**
     * Get database credentials as a Zend Db Adapter configuration
     * @return array[]
     */
    private function _getDatabaseConnection()
    {

        return [
            'adapter' => [
                'driver' => 'Pdo_Mysql',
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'charset' => 'utf8'
            ]
        ];
    }
}
