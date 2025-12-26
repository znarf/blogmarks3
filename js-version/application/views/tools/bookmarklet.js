var q='';
var r='';

if(document.selection)
	q = document.selection.createRange().text;
else if(window.getSelection)
	q = window.getSelection();

if(document.referrer)
	r = document.referrer;

void(

open(

	'http://{BM_HOST}/my/marks,new?mini=1'
	+
	'&title='+encodeURIComponent(document.title)
	+
	'&url='+encodeURIComponent(location.href)
	+
	'&description='+encodeURIComponent(q)
	+
	'&via='+encodeURIComponent(r)
	,
	'blogmarks3'
	,
	'location=no,toolbar=no,scrollbars=yes,width=350,height=500,status=no'

)

);
