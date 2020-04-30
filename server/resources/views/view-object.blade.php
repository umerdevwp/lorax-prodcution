<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Google Cloud Object</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    </head>
    <body>

      <div class="container">
          <div class="row justify-content-center">
              <div class="col-md-8">
                  <div class="card">
                      <div class="card-header">{{ __('View PDF') }}</div>

                      <div class="card-body">
                           <iframe id='pdf' src=""></iframe>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </body>
    <script>
            var pdf = document.querySelector('#pdf');
            var url = "{{ $url }}";
            var key = "{{ $key }}";
            var keysha = "{{ $keySHa256 }}";

            populateIframe(pdf, url, [
                ['Access-Control-Allow-Origin','*'],
                ['x-goog-encryption-algorithm', 'AES256'],
                ['x-goog-encryption-key', key],
                ['x-goog-encryption-key-sha256', keysha]
                 ]);

            function populateIframe(iframe, url, headers) {
            var xhr = new XMLHttpRequest();

            xhr.open('GET', url);
            xhr.onreadystatechange = handler;
            xhr.responseType = 'blob';
            headers.forEach(function(header) {
                xhr.setRequestHeader(header[0], header[1],header[2],header[3]);
            });
            xhr.send();

            function handler() {
                if (this.readyState === this.DONE) {
                if (this.status === 200) {
                    // this.response is a Blob, because we set responseType above
                    iframe.src = URL.createObjectURL(this.response);
                } else {
                    console.error('XHR failed', this);
                }
                }
            }


            }

            function setPDF(blob) {
            document.querySelector('#blob').src = URL.createObjectURL(blob);
            }
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>

</html>