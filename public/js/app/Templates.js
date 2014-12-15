define(['underscore'], function (_)
{
    var Templates = {};

    Templates.jqGrid = {
        tonerCost : _.template('<%- dealerCost %><br><%- systemCost %>'),
        tonerSku  : _.template('<%- dealerSku %><br><%- systemSku %>'),
        tonerColor: _.template('<img class="<%- classes %>" src="<%- source %>" alt="<%- colorName %>"><br><%- colorName %>')
    };

    Templates.data = {
        tonerColors: [
            //@formatter:off
            { },
            { name: 'Black',   image: '/img/tonercolors/Black.png',   class: 'toner-color-black'   },
            { name: 'Cyan',    image: '/img/tonercolors/Cyan.png',    class: 'toner-color-cyan'    },
            { name: 'Magenta', image: '/img/tonercolors/Magenta.png', class: 'toner-color-magenta' },
            { name: 'Yellow',  image: '/img/tonercolors/Yellow.png',  class: 'toner-color-yellow'  },
            { name: '3 Color', image: '/img/tonercolors/3color.png',  class: 'toner-color-3color'  },
            { name: '4 Color', image: '/img/tonercolors/4color.png',  class: 'toner-color-4color'  }
            //@formatter:on
        ]
    };

    return Templates;
});