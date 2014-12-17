define(['underscore'], function (_)
{
    var Templates = {};

    Templates.jqGrid = {
        tonerCost : _.template('<%- dealerCost %><br><%- systemCost %>'),
        tonerSku  : _.template('<%- dealerSku %><br><%- systemSku %>'),
        tonerColor: _.template('<i class="fa fa-fw fa-2x <%- classes %>" title="<%- colorName %>"></i><br><small><%- colorName %></small>')
    };

    Templates.data = {
        tonerColors: [
            //@formatter:off
            { },
            { name: 'Black',   class: 'fa-toner-color-black'       },
            { name: 'Cyan',    class: 'fa-toner-color-cyan'        },
            { name: 'Magenta', class: 'fa-toner-color-magenta'     },
            { name: 'Yellow',  class: 'fa-toner-color-yellow'      },
            { name: '3 Color', class: 'fa-toner-color-three-color' },
            { name: '4 Color', class: 'fa-toner-color-four-color'  }
            //@formatter:on
        ]
    };

    return Templates;
});