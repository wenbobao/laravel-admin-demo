<?php

namespace App\Admin\Controllers;

use App\Models\Movie;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class MovieController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Movie);

        $grid->model()->orderBy('id', 'desc');
        // $grid->id('Id');
        $grid->id('Id')->sortable();
        // $grid->title('Title');
        $grid->column('title', 'Title');
        $grid->director('Director');
        $grid->describe('Describe');
        // $grid->rate('Rate');
        // 判断type来显示不同的状态
        $grid->column('rate','类型？')->display(function ($type) {
            return $type == 1 ? 'YES' : 'NO';
        });
        $grid->released('Released');
        $grid->release_at('Release at');
        // $grid->created_at('Created at');
        // $grid->updated_at('Updated at');
        // 禁用创建按钮
        $grid->disableCreateButton();
        // 禁用分页条
        // $grid->disablePagination();
        $grid->perPages([10, 20, 30, 40, 50]);
        // 禁用查询过滤器
        // $grid->disableFilter();
        // 禁用导出数据按钮
        $grid->disableExport();
        // 禁用行操作列
        // $grid->disableActions();

        // $grid->actions(function ($actions) {
        //     // 隐藏删除按钮
        //     $actions->disableDelete();
        //     // 隐藏修改按钮
        //     $actions->disableEdit();
        // });

        // 添加自定义操作按钮
        // $grid->actions(function ($actions) {
        //     // append一个操作
        //     $actions->append('<a href=""><i class="fa fa-eye"></i></a>');
        //     // prepend一个操作
        //     $actions->prepend('<a href=""><i class="fa fa-paper-plane"></i></a>');
        // });

        // 关闭批量删除操作
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->filter(function ($query) {
            // 去掉默认的id过滤器
            $query->disableIdFilter();
        
            //1.时间段筛选   设置created_at字段的范围查询
            $query->between('created_at', '筛选时间')->datetime();
            //2.字段模糊查询 like = '% %'
            $query->like('title', '文章标题');
            //3.字段equal 筛选
            $query->equal('rate', '状态')->select([0 => 'NO', 1 => 'YES']);

            // $query->equal('cate_id', '所属分类')->select(
            //     ArticleCategories::pluck('name', 'id')
            // );
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Movie::findOrFail($id));

        $show->id('Id');
        $show->title('Title');
        $show->director('Director');
        $show->describe('Describe');
        $show->rate('Rate');
        $show->released('Released');
        $show->release_at('Release at');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Movie);

        $form->hidden('is_in');
        // $form->text('title', 'Title');
        // 添加校验规则
        $form->text('title', '标题')->rules('required');
        // 上传图片
        $form->image('thumb', '文章缩略图')
             ->uniqueName()
             ->rules('mimes:png')
             ->help("上传文件快点快点快点看快点开")
             ->move('upload/article/' . date("Ymd"))
             ->options(['overwriteInitial' => true]);
        $form->number('director', 'Director');
        $form->text('describe', 'Describe');
        // $form->switch('rate', 'Rate');
        $states = [
            'on'  => ['value' => 1, 'text' => '上线', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '下线', 'color' => 'danger'],
        ];
        $form->switch('rate', '上/下线')->states($states);

        $form->text('released', 'Released');
        $form->datetime('release_at', 'Release at')->default(date('Y-m-d H:i:s'));
        // 去掉重置按钮
        $form->disableReset();

        $form->url('user.website', '官网')
                #默认填充url 传参
                ->default('')
                #提示的url
                ->help('eg: http://www.aware.bi');
        
        //表单提交下拉框
        $form->multipleSelect("select", "多选框")->options(["1", "2"]);
        // select
        $types = array('0'=>'教育','1'=>'医疗');
        $form->select('type', '类型')->options($types);
        // 编辑器
        // $form->editor('detail', '详细介绍');

        // 保存前回调
        $form->saving(function (Form $form) {
 
        });
        // 保存后回调
        $form->saved(function (Form $form) {
 
        });
        return $form;
    }
}
