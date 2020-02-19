function is_delete_key(key) { return key == 8; }
function is_enter_key(key) { return key == 13; }

function number_of_answers() {
	return $('#answers > div').length;
}

function is_empty(id) {
	return $('#'+id).val()=='';
}

function can_be_deleted(id) {
	return number_of_answers() > 2 && is_empty(id);
}

function get_values() {
	var values = [];
  $('#answers input').each(function() {values.push($(this).val()); });
	return values;
}

function add_answer(id, text) {
	var html = '<div class="form-group">'
           + ' <div class="input-group">'
           + '  <div class="input-group-addon">'+id+'</div>'
					 + '  <input id="'+id+'" type="text" class="form-control" ' 
           + '         onkeydown="on_keydown(event, '+id+')"'
           + '         name="answers[]" value="'+text+'">'
           + ' </div>'
           + '</div>';
	$('#answers').append(html);
}

function create_answers(values) {
	$('#answers').html('');
	for (var id = 0; id < values.length; id++) 
    add_answer(id+1, values[id]);
}

function set_focus(id) {
	setTimeout(function() {
    var input = $('#'+id);
    var val = input.val();
		input.focus().val("").val(val);
  });
}

function on_form_keypress(event) {
	if (is_enter_key(event.which)) event.preventDefault();
}	

function on_remove_answer(id) {
	var values = get_values();
  values.splice(id-1,1);
  create_answers(values);
	var focus_id = id-1;
	if (focus_id < 1) focus_id = 1;
	set_focus(focus_id);
}

function on_add_answer_after(id) {
	var values = get_values();
  values.splice(id,0,'');
  create_answers(values);
	set_focus(id+1);
}

function on_keydown(event, id) {
	if (is_enter_key(event.which)) {
		on_add_answer_after(id);
		event.preventDefault();
	}
  else if (is_delete_key(event.which) && can_be_deleted(id)) {
		on_remove_answer(id);
    event.preventDefault();
	}
}
