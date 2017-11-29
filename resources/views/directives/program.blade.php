<span ng-class="{'b-like': level == 1}" ng-if="level">
    @{{ levelstring }} @{{ item.title }}
</span>
<ul ng-if="item.content.length" class="program-list-item" ng-class="{'list-main': !level }">
    <li ng-repeat="child in item.content">
        <program-item item="child" level="level + 1" levelstring="getChildLevelString($index)"></program-item>
    </li>
</ul>
