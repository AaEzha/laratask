<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use GroceryCrud\Core\GroceryCrud;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function projects()
    {
        $crud = $this->_getGroceryCrudEnterprise();

        $crud->setTable('project_members');
        $crud->setSubject('Project', 'My Projects');
        $crud->unsetOperations();
        $crud->where(['user_id' => Auth::id()]);
        $crud->unsetColumns(['created_at', 'user_id']);
        $crud->setRelation('project_id', 'projects', 'name');
        $crud->displayAs([
            'project_id' => 'Project Name'
        ]);
        $crud->unsetSearchColumns(['project_id']);
        $crud->callbackColumn('project_id', function ($value, $row) {
            $project = Project::find($value);
            return "<a href='".route('user.project', $project->id)."'>".$project->name."</a>";
        });
        $crud->setActionButton('Project Detail', 'fa fa-info', function ($row) {
            $project = ProjectMember::find($row->id);
            return route('user.project', $project->project_id);
        }, false);

        $output = $crud->render();

        return $this->_showOutput($output);
    }

    public function tasks()
    {
        $crud = $this->_getGroceryCrudEnterprise();

        $crud->setTable('project_tasks');
        $crud->setSubject('Project', 'Assigned Tasks');
        $crud->unsetOperations();
        $crud->where(['user_id' => Auth::id()]);
        $crud->columns(['name', 'project_id', 'due_date']);
        $crud->setRelation('project_id', 'projects', 'name');
        $crud->displayAs([
            'project_id' => 'Project Name'
        ]);
        $crud->unsetSearchColumns(['project_id']);
        $crud->setActionButton('Mark as complete', 'fa fa-check', function ($row) {
            $project = ProjectMember::find($row->id);
            return route('user.project', $project->project_id);
        }, false);

        $output = $crud->render();

        return $this->_showOutput($output);
    }

    public function project(Project $project)
    {
        return view('user.project', compact('project'));
        dd($project);
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
