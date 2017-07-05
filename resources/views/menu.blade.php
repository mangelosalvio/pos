<paper-menu>
    @if(Auth::user()->hasRole('admin'))
        <paper-item ng-click="link('tablefile');">
            Tables
        </paper-item>

        <paper-item ng-click="link('products')">
            Products
        </paper-item>

        <paper-item ng-click="link('category')">
            Categories
        </paper-item>

        <paper-item ng-click="link('sale-inventory')">
            Sale Reports
        </paper-item>

        <paper-item ng-click="link('users')">
            Users
        </paper-item>
    @endif

    <paper-item ng-click="link('')">
        Sales
    </paper-item>
    <paper-item ng-click="link('sales')">
        Search Sales
    </paper-item>
</paper-menu>