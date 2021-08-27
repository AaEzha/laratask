<?php

namespace App\Http\Controllers;

use App\Mail\NewRegistered;
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
        $crud->callbackBeforeInsert(function($s){
            $s->data['created_at'] = now();
            $s->data['updated_at'] = now();
            return $s;
        });
        $crud->callbackBeforeUpdate(function($s){
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
        $crud->callbackBeforeInsert(function($s){
            $s->data['created_at'] = now();
            $s->data['updated_at'] = now();
            $s->data['password_shadow'] = $s->data['password'];
            $s->data['password'] = bcrypt($s->data['password']);
            return $s;
        });
        $crud->callbackBeforeUpdate(function($s){
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

    /**
     * Get everything we need in order to load Grocery CRUD
     *
     * @return GroceryCrud
     * @throws \GroceryCrud\Core\Exceptions\Exception
     */
    private function _getGroceryCrudEnterprise() {
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
    private function _showOutput($output) {
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
    private function _getDatabaseConnection() {

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
