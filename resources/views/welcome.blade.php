<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <title></title>
  </head>
  <body class="bg-gray-50">

    <div class="">

      <!-- Nav bar -->
      <div class="h-16 flex border-b border-gray-300 z-40 bg-white">
        <div class="p-2">
          <img class="object-contain h-full w-48" src="{{url('images/itri.png')}}" alt="Image">
        </div>
      </div>

      <!-- content -->
      <div class="p-2 space-y-2">
        <div class="h-10 bg-gray-200">
          Type Title
        </div>

        <div class="h-16 bg-gray-200">
          Type Button
        </div>

        <div class="h-10 bg-gray-200">
          List Title
        </div>

        <div class="h-32 bg-gray-200">
          List
        </div>

        <div class="h-10 bg-gray-200">
          Order Info. Title
        </div>

        <div class="h-8 bg-gray-200">
          select list
        </div>

        <div class="h-8 bg-gray-200">
          input box
        </div>

        <div class="h-8 bg-gray-200">
          clear
        </div>

        <div class="h-48 bg-gray-200">
          Order Info.
        </div>

        <div class="h-8 bg-gray-200">
          Submit
        </div>

      </div>

    </div>

  </body>
  <script src="{{ asset('js/app.js') }}"></script>
</html>
