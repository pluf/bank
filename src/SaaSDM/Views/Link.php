<?php
Pluf::loadFunction ( 'SaaSDM_Shortcuts_GetLinkOr404' );

class SaaSDM_Views_Link {
	public static function create($request, $match) {
		$asset = SaaSDM_Shortcuts_GetAssetOr404($match['asset_id']);
		
		// initial link data
		$extra = array (
				// 'user' => $request->user,
				'tenant' => $request->tenant,
				'asset' => $asset
		);
		
		// Create link and get its ID
		$form = new SaaSDM_Form_LinkCreate( $request->REQUEST, $extra );
		$link = $form->save ();
		return new Pluf_HTTP_Response_Json ( $link );
	}
	
	public static function get($request, $match) {
		$link = new SaaSDM_Link ( $match ['id'] );
		return new Pluf_HTTP_Response_Json ( $link );
	}
	
	public static function find($request, $match) {
		$links = new Pluf_Paginator ( new SaaSDM_Link () );
		$sql = new Pluf_SQL ( 'tenant=%s', array (
				$request->tenant->id 
		) );
		$links->forced_where = $sql;
		$links->list_filters = array (
				'id',
				'secure_link',
				'expiry',
				'download',
				'asset' 
		);
		$search_fields = array (
				'id',
				'secure_link',
				'expiry',
				'download',
				'asset' 
		);
		$sort_fields = array (
				'id',
				'secure_link',
				'expiry',
				'download',
				'asset' 
		);
		$links->configure ( array (), $search_fields, $sort_fields );
		$links->items_per_page = 20;
		$links->setFromRequest ( $request );
		return new Pluf_HTTP_Response_Json ( $links->render_object () );
	}
	public static function download($request, $match) {
		$link = SaaSDM_Shortcuts_GetLinkBySecureIdOr404( $match ['secure_link'] );
		if ($link->tenant != $request->tenant->id) {
			// Error 404
		}
		// TODO: check link expiry
		$asset = $link->get_asset ();
		
		// update download
		$link->download ++;
		$link->update();
		// XXX: DO download
		$httpRange = isset($request->SERVER['HTTP_RANGE']) ? $request->SERVER['HTTP_RANGE'] : null;
		$response =  new Pluf_HTTP_Response_ResumableFile($asset->path . '/' . $asset->id, $httpRange, $asset->name, $asset->mime_type);
		// TODO: do buz.
		$size = $response->computeSize();
		
		
		
		return $response;
	}
}