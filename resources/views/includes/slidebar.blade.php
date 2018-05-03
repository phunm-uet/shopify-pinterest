<div class="nav-side-menu">
    <div class="brand">Brand Logo</div>
    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
  
        <div class="menu-list">
  
            <ul id="menu-content" class="menu-content collapse out">
                <li class="{{Request::route()->getName() == 'home' ? 'active' : '' }}">
                  <a href="{{route('home')}}">
                  <i class="fa fa-dashboard fa-lg"></i> Dashboard
                  </a>
                </li>
                 <li class="{{Request::route()->getName() == 'history' ? 'active' : '' }}">
                  <a href="{{route('history')}}">
                    <i class="fa fa-user fa-lg"></i> History
                  </a>
                  </li>
            </ul>
     </div>
</div>