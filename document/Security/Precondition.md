
# پیش شرط‌ها

فراخوانی هر متد از لایه نمایش نیازمند یک سری پیش شرط‌ها است. با استفاده از این پیش شرط‌ها می‌توان امنیت را در لایه نمایش فراهم کرد.

هر پیش شرط با استفاده از یک رشته آدرس دهی می‌شود. این رشته به صورت زیر است:

	{Class name}::{method name}

این پیش شرط در حقیقت یک رشته است که یک متد از یک کلاس را آدرس دهی می‌کند.

## تعریف پیش شرط

یک پیش شرط به صورت زیر تعریف می‌شود:

	var $XXX_precond = ...

که در اینجا XXX نام متد در لایه نمایش است.

برای نمونه پیش شرط مدیریت سیستم به صورت زیر است:

	var $xxx_precond = 'Pluf_Precondition::adminRequired'

مقدار پیش شرط می‌تواند یک یا یک آرایه از پیش شرطها باشد.

	var $xxx_precond = array('Pluf_Precondition::adminRequired');
	
برخی پیش شرطها پارامترهایی را نیاز دارند. در این حالت پیش شرط به صورت آرایه تعریف می‌شود که در ادامه آن پارامترهای آن آورده می‌شود:

	var $xxx_precond = array(
		'Pluf_Precondition::loginRequired',
		array(
			'Pluf_Precondition::hasPerm',
			'{permission id}'
		),
	);