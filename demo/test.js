function change(){
	var db=new CheapDB();
	db.setDB("songs");
	db.selectId(1);
	var id=1;
	var song=db.selectKey("song");
	var artist=db.selectKey("artist");
	var first_p=document.getElementsByTagName("p").item(0);
	var sec_p=document.getElementsByTagName("p").item(1);
	var div_next=document.getElementsByTagName("div").item(0);
	var div_prev=document.getElementsByTagName("div").item(1);
	first_p.innerHTML=song;
	sec_p.innerHTML=artist;
	div_next.onclick=function(){
		id++;
		if(db.selectId(id)){
			var song=db.selectKey("song");
			var artist=db.selectKey("artist");
			first_p.innerHTML=song;
			sec_p.innerHTML=artist;
		}
		else{
			id--;
		}
	};
	div_prev.onclick=function(){
		id--;
		if(db.selectId(id)){
			var song=db.selectKey("song");
			var artist=db.selectKey("artist");
			first_p.innerHTML=song;
			sec_p.innerHTML=artist;
		}
		else{
			id++;
		}
	};
};
function hide_and_seek(e){
	var initial=e.style.opacity;
	while(initial>0){
		initial--;
		e.style.opacity=initial;
		console.log(initial);
	}
	while(initial<100){
		initial++;
		e.style.opacity=initial;
	}
};
window.onload=function(){
	change();
};
