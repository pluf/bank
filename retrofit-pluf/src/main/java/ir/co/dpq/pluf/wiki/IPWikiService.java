package ir.co.dpq.pluf.wiki;

import retrofit.Callback;
import retrofit.http.GET;
import retrofit.http.Path;

/**
 * دسترسی به صفحه‌های ویکی را فراهم می‌کند
 *
 * صفحه‌های ویکی به عنوان یک منبع راهنما برای کاربران ایجاد شده است.
 * 
 * این واسط ابزارهای مورد نیاز برای دسترسی به این صفحه‌ها را فراهم کرده است.
 * 
 * 
 * @author maso <mostafa.barmshory@dpq.co.ir>
 *
 */
public interface IPWikiService {

	/**
	 * یک صفحه ویکی را بازیابی می‌کند.
	 * 
	 * @param lang
	 * @param pageId
	 * @param callback
	 */
	@GET("/api/wiki/{language}/{pageId}")
	void getWikiPage(@Path("language") String lang, @Path("pageId") String pageId, Callback<PWikiPage> callback);

	/**
	 * یک صفحه ویکی را بازیابی می‌کند
	 * 
	 * @see #getWikiPage(String, String, Callback)
	 * @param lang
	 * @param pageId
	 * @return
	 */
	@GET("/api/wiki/{language}/{pageId}")
	PWikiPage getWikiPage(@Path("language") String lang, @Path("pageId") String pageId);
}