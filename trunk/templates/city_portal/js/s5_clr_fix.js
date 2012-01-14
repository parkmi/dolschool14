var s5_div_clr =
document.getElementById("s5_maincolumn").getElementsByTagName("DIV");
        for (var s5_div_clra=0; s5_div_clra<s5_div_clr.length; s5_div_clra++) {
        if (s5_div_clr[s5_div_clra].className) {
        s5_div_clr[s5_div_clra].className = "";
        }
        }