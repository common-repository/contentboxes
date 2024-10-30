//======================================
// @Description: Add a contentbox
// @Require: id
// @Require: title
// @Require: configpath
function add_contentbox(id, title, configpath) {
	jQuery('#contentboxes').append('<li class="remove_cb" id="cb_act_'+ id +'" ><span class="remove_box" onClick="remove_contentbox(\''+ id +'\');" title="Add Contentbox">&nbsp;</span><a href="' + configpath + '&amp;TB_iframe=true" class="thickbox">'+ title +'</a><input type="hidden" name="contentbox[]" id="cb_'+ id +'" value="'+ id +'" /></li>');
	jQuery('#add_' + id).hide();
	tb_init('#contentboxes #cb_act_'+ id +' a');
}

//======================================
// @Description: Remove a contentbox
// @Require: id
function remove_contentbox(id){
	jQuery('#cb_act_' + id).remove();
	jQuery('#add_' + id).css('display','block');
}

//======================================
// @Description: serialize() for javascript
// @Require: array a
// @Return: str serialized
function serializeArray(a)
{
	var serializedString = '';
	var arrayLength = 0;
	for(var aKey in a)
	{
		//key definition
		if(aKey * 1 == aKey) //is_numeric?
		{
			//integer keys look like i:key
			serializedString += 'i:' + aKey + ';';	
		}
		else
		{
			//string keys look like s:key_length:key;
			serializedString += 's:' + aKey.length + ':"' + aKey + '";';
		}
		
		//value definition
		if(a[aKey] * 1 == a[aKey])
		{
			//integer value look like i:value
			serializedString += 'i:' + a[aKey] + ';';	
		}
		else if(typeof(a[aKey]) == "string")
		{
			//string value look like s:key_length:value;
			serializedString += 's:' + a[aKey].length + ':"' + a[aKey] + '";';
		}
		else if(a[aKey] instanceof Array)
		{
			serializedString += serializeArray(a[aKey]);
		}
		arrayLength++;
	}
	serializedString = 'a:' + arrayLength + ':{' + serializedString + '}';
	
	return serializedString;
}

//======================================
// @Description: Run on DOMREADY
jQuery(document).ready(function(){
	jQuery(".sortable").sortable({});
});
