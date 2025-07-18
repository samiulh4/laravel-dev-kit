 @if (session('success'))
     <div class="alert alert-success alert-dismissible" role="alert">
         <h4 class="alert-heading d-flex align-items-center flex-wrap gap-1">
             <span class="alert-icon rounded-circle"><i class="icon-base bx bx-coffee"></i></span>Success :)
         </h4>
         <hr />
         <p class="mb-0">{{ session('success') }}</p>
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
 @endif
 @if (session('error'))
     <div class="alert alert-danger alert-dismissible" role="alert">
         <h4 class="alert-heading d-flex align-items-center flex-wrap gap-1">
             <span class="alert-icon rounded-circle"><i class="icon-base bx bx-error"></i></span>Error!!
         </h4>
         <hr />
         <p>{{ session('error') }}</p>
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
 @endif
 @if ($errors->any())
     <div class="alert alert-danger">
         <ul class="mb-0">
             @foreach ($errors->all() as $error)
                 <li>{{ $error }}</li>
             @endforeach
         </ul>
     </div>
 @endif
