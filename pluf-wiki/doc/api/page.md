# ویکی صفحه

## فهرست صفحه‌ها

	/page/list
	method: GET

## ایجاد صفحه

	/page/create
	method: POST

## حذف یک صفحه

	/page/{page id}
	method: DELETE

## به روز کردن یک صفحه

	/page/{page id}
	method: POST

## گرفتن صفحه

	/page/{page id}
	method: GET

## گرفتن محتوی یک صفحه

برای گرفتن محتوای یک صفحه فراخوانی زیر در نظر گرفته شده است:

	/{language}/{page id}

در این فراخوانی دو متغیر زبان و شناسه صفحه نیز در نظر گرفته شده که در اینجا شناسه همان نام صفحه یا عنوان آن است.

این فراخوانی تنها با استفاده از متد GET استفاده می‌شود که نتیجه آن اطلاعات کامل یک صفحه است. یک نمونه خروجی از این فراخوانی در زیر آورده شده است.

	{
	  "id": "",
	  "title": "about",
	  "language": "en",
	  "summary": "",
	  "content": "# About us\n\nThis is simple text file.",
	  "creation_dtime": "2015-05-16 16:50:00",
	  "modif_dtime": "2015-05-16 16:50:00"
	}
