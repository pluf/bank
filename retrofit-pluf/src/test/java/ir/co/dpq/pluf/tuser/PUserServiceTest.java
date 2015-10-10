package ir.co.dpq.pluf.tuser;

import java.net.CookieHandler;
import java.net.CookieManager;
import java.net.CookiePolicy;
import java.util.HashMap;
import java.util.Map;

import static org.junit.Assert.*;
import org.junit.Before;
import org.junit.Test;

import ir.co.dpq.pluf.PErrorHandler;
import ir.co.dpq.pluf.PException;
import ir.co.dpq.pluf.user.IPUserService;
import ir.co.dpq.pluf.user.PUser;
import retrofit.RestAdapter;

public class PUserServiceTest {

	private IPUserService usr;

	@Before
	public void createService() {
		CookieManager cookieManager = new CookieManager();
		cookieManager.setCookiePolicy(CookiePolicy.ACCEPT_ALL);
		CookieHandler.setDefault(cookieManager);

		String API_URL = "http://localhost:1396";
		RestAdapter restAdapter = new RestAdapter.Builder()
				// تعیین کنترل کننده خطا
				.setErrorHandler(new PErrorHandler())
				// تعیین آدرس سایت مورد نظر
				.setEndpoint(API_URL)
				// ایجاد یک نمونه
				.build();
		this.usr = restAdapter.create(IPUserService.class);
	}

	@Test
	public void getSessionUser() {
		PUser user = usr.getSessionUser();
		assertNotNull(user);
	}

	@Test
	public void login() {
		PUser user = usr.login("admin", "admin");
		assertNotNull(user);
		assertEquals("admin", user.getLogin());
	}

	@Test(expected = PException.class)
	public void loginFail() {
		PUser user = usr.login("Non user name", "bad password");
		assertNotNull(user);
		assertEquals("admin", user.getLogin());
	}

	@Test
	public void logout() {
		// Login
		PUser user = usr.login("admin", "admin");
		assertNotNull(user);
		assertEquals("admin", user.getLogin());

		usr.logout();
	}

	@Test
	public void updateUserFirstName() {
		PUser user = usr.login("admin", "admin");
		assertNotNull(user);
		assertEquals("admin", user.getLogin());

		String name = "maostafa" + Math.random();

		Map<String, Object> params = new HashMap<String, Object>();
		params.put("first_name", name);
		PUser nuser = usr.update(params);
		assertNotNull(nuser);
		assertEquals(name, nuser.getFirstName());
	}
	
	@Test
	public void updateUserEmail() {
		PUser user = usr.login("admin", "admin");
		assertNotNull(user);
		assertEquals("admin", user.getLogin());
		
		String email = "mostafa.barmshory@dpq.co.ir";
		
		Map<String, Object> params = new HashMap<String, Object>();
		params.put("email", email);
		PUser nuser = usr.update(params);
		assertNotNull(nuser);
	}
}
