<?php
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
Pluf::loadFunction('Pluf_HTTP_URL_urlForView');
Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
Pluf::loadFunction('KM_Shortcuts_GetCategoryOr404');

/**
 *
 * @date 1394
 *
 * @author maso <mostafa.barmshory@dpq.co.ir>
 */
class KM_Views_Category
{

    public function find ($request, $match)
    {
        $count = 20;
        // maso, 1394: گرفتن فهرست مناسبی از پیام‌ها
        // Paginator to paginate messages
        $pag = new Pluf_Paginator(new KM_Category());
        // $pag->forced_where = new Pluf_SQL('user=%s',
        // array(
        // $request->user->id
        // ));
        $list_display = array(
                'title' => __('title'),
                'description' => __('description'),
                'color' => __('color')
        );
        $search_fields = array();
        $sort_fields = array(
                'creation_date'
        );
        $pag->configure($list_display, $search_fields, $sort_fields);
        $pag->action = array(
                'Label_Views_Label::label'
        );
        $pag->items_per_page = $count;
        $pag->no_results_text = __('queue is empty');
        $pag->sort_order = array(
                'creation_dtime',
                'DESC'
        );
        $pag->setFromRequest($request);
        return new Pluf_HTTP_Response_Json($pag->render_object());
    }

    public function create ($request, $match)
    {
        $parent = $this->internalGetRootCategory($request, $match);
        $extra = array(
                'user' => $request->user,
                'parent' => $parent
        );
        $form = new KM_Form_CategoryCreate(
                array_merge($request->REQUEST, $request->FILES), $extra);
        $cat = $form->save();
        return new Pluf_HTTP_Response_Json($cat);
    }

    public function createSubCategory ($request, $match)
    {
        $parent = KM_Shortcuts_GetCategoryOr404($match[1]);
        $extra = array(
                'user' => $request->user,
                'parent' => $parent
        );
        $form = new KM_Form_CategoryCreate(
                array_merge($request->REQUEST, $request->FILES), $extra);
        $cat = $form->save();
        return new Pluf_HTTP_Response_Json($cat);
    }

    public function update ($request, $match)
    {
        $cat = KM_Shortcuts_GetCategoryOr404($match[1]);
        $extra = array(
                'user' => $request->user,
                'parent' => null,
                'category' => $cat
        );
        $form = new KM_Form_CategoryUpdate(
                array_merge($request->REQUEST, $request->FILES), $extra);
        $cat = $form->update();
        return new Pluf_HTTP_Response_Json($cat);
    }

    public function delete ($request, $match)
    {
        $cat = KM_Shortcuts_GetCategoryOr404($match[1]);
        $d = new KM_Category($cat->id);
        $d->delete();
        return new Pluf_HTTP_Response_Json($cat);
    }

    public function root ($request, $match)
    {
        $root = $this->internalGetRootCategory($request, $match);
        return new Pluf_HTTP_Response_Json($root);
    }

    public function get ($request, $match)
    {
        $cat = KM_Shortcuts_GetCategoryOr404($match[1]);
        return new Pluf_HTTP_Response_Json($cat);
    }

    public function children ($request, $match)
    {
        $cat = KM_Shortcuts_GetCategoryOr404($match[1]);
        $count = 20;
        // maso, 1394: گرفتن فهرست مناسبی از پیام‌ها
        // Paginator to paginate messages
        $pag = new Pluf_Paginator(new KM_Category());
        $pag->forced_where = new Pluf_SQL('parent=%s', 
                array(
                        $cat->id
                ));
        $list_display = array(
                'title' => __('title'),
                'description' => __('description'),
                'color' => __('color')
        );
        $search_fields = array();
        $sort_fields = array(
                'creation_date'
        );
        $pag->configure($list_display, $search_fields, $sort_fields);
        $pag->items_per_page = $count;
        $pag->no_results_text = __('queue is empty');
        $pag->sort_order = array(
                'creation_dtime',
                'DESC'
        );
        $pag->setFromRequest($request);
        return new Pluf_HTTP_Response_Json($pag->render_object());
    }

    private function internalGetRootCategory ($request, $match)
    {
        $root = Pluf::factory('KM_Category')->getOne(
                array(
                        'filter' => 'parent=0'
                ));
        return $root;
    }
}