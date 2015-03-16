function move_yk(){
	var last = $('#yk_dt p').last();
	$('#yk_dt p').last().remove();
	$('#yk_dt p').first().animate({marginTop:'50px'},1000).animate({marginTop:'0px'},0).queue(function(){$(this).before(last);});
}
$(function(){
	setInterval(move_yk,5000);
})