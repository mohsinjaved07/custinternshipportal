DATE = document.getElementById("DATE1").value;
document.getElementById("DATE1").min = DATE;

const d2 = new Date(DATE);
d2.setDate(d2.getDate() + 45);
year = d2.getFullYear();
month = d2.getMonth() + 1;
month = month < 10 ? '0' + month : month;
date = d2.getDate();
date = date < 10 ? '0' + date : date;
DATE = year + "-" + month + "-" + date;

document.getElementById("DATE2").min = DATE;
document.getElementById("DATE2").value = DATE;