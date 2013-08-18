  var j,d="",l="",m="",p="",q="",z="",list= new Array()
  list[list.length]='images/header1.jpg?&border=0';
  list[list.length]='images/header2.jpg?&border=0';
  list[list.length]='images/header3.jpg?&border=0';
  list[list.length]='images/header4.jpg?&border=0';
  list[list.length]='images/header5.jpg?&border=0';
  list[list.length]='images/header6.jpg?&border=0';
  j=parseInt(Math.random()*list.length);
  j=(isNaN(j))?0:j;
  if (list[j].indexOf('?')==-1) {
    document.write("<img src='"+list[j]+"'>");
  }
  else {
    nvp=list[j].substring(list[j].indexOf('?')+2).split('&');
    for(var i=0;i<nvp.length;i++) {
      sub=nvp[i].split('=');
   	  switch(sub[0]) {
 	    case 'link':
          l="<a href='"+unescape(sub[1])+"'>";
          p="</a>";
		  break;
	    case 'target':
          q=" target='"+unescape(sub[1])+"'";
  		  break;
  	    default:
          m+=" "+sub[0]+"='"+unescape(sub[1])+"'";
  		  break;
      }
    }
    z=(l!="")?((q!="")?l.substring(0,l.length-1)+q+">":l):"";
    z+="<center><img src='"+list[j].substring(0,list[j].indexOf('?'))+"'"+m+"></center>"+p;
  document.write(z);
  }