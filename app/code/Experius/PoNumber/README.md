Experius Ponumber

Adds ponumber field to

Editable
- Checkout
- Admin Order Create

View
- Admin Order Grid (optional)
- Admin Order View

Ponumber is saved in

quote table > experius_po_number
order table > experius_po_number

You can add the ponumber to the email template

{{depend order.getExperiusPoNumber()}}
    {{var order.getExperiusPoNumber()}}
{{/depend}}

API

salesOrderRepositoryV1

- GET /V1/orders/{id}
- GET /V1/orders

"extension_attributes": {
    "experius_po_number": "23435463563"
}

Roadmap.

- Make configurable in wich section to of the checkout to view.
- length validation