	var s5_fa_box = document.getElementById("s5_box_sm_inner").getElementsByTagName("H3");
	for (var fa_box=0; fa_box<s5_fa_box.length; fa_box++) {
		document.getElementById("s5_login").innerHTML = s5_fa_box[fa_box].innerHTML;
		s5_fa_box[fa_box].style.display = "none";
		break;
	}
