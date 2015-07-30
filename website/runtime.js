$('a.backToTop').click(function(link){
        link.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        }, 500);
        return false;
});

defaultStyle = {
    background: "#fefefe",
    border: {
        width: 5
    },
    borderColor: "#000",
    color: "#000",
    lineHeight: 1.5,
    padding: 10,
    textAlign: "left",
    tip: true,
    width: 300,
    name: 'dark'
};

bigOrange = {
    background: "#fff",
    border: {
        color: "#ff8200",
        width: 5
    },
    color: "#000",
    lineHeight: 1.5,
    padding: 10,
    textAlign: "center",
    tip: true,
    width: 300,
    name: 'dark'
};

$('a[title]').not('a.sticky, a.flip, a.bigorange').qtip({
    position: {	corner: { target:'bottomMiddle', tooltip: 'topMiddle' } },
    style: defaultStyle
});

$('a[title].bigorange').qtip({
    position: {	corner: { target:'bottomMiddle', tooltip: 'topMiddle' } },
    style: bigOrange
});

$('a[title].flip').qtip({
    position: {	corner: { target:'topMiddle', tooltip: 'bottomMiddle' } },
    style: defaultStyle
});

$('a[title].sticky').qtip({
    hide: 'unfocus',
    position: {	corner: { target:'bottomMiddle' } },
    style: defaultStyle
});
