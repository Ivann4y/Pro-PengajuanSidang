<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/style.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        button {
            border: none;
            border-radius: 50px;
            background-color: #D9D9D9;
            color: #626262;
            transition: 300ms;
        }

        button:hover {
            background-color: rgb(67, 54, 240);
            color: white;
        }

        .bgBiru {
            background-color: rgb(67, 54, 240);
            height: 50vh;
        }

        .fullscreen {
            height: 100vh;
            position: relative;
        }

        .letak-logBox {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
        }
    </style>
</head>

<body>
    <!-- Fullscreen -->
    <div class="fullscreen">
        <!-- Header Section -->
        <div class="bgBiru">
            <div class="text-white text-center py-5 fixed-top" style="z-index: 999;">
                <h2><strong>Sistem Pengajuan Sidang</strong></h2>
                <h2><strong>Politeknik Astra</strong></h2>
            </div>
        </div>

        <!-- Login Box -->
        <div class="letak-LogBox">
            <div class="container text-dark">
                <div class="row justify-content-center">
                    <div class="col-md-5 text-center bg-white p-4 rounded rounded-5 shadow">
                        <div class="my-2 p-2" style="color: rgb(67, 54, 240);">
                            <h1>Masuk Sebagai</h1>
                        </div>
                        <div class="role-button d-grid gap-3 mb-4 p-2">
                            <div>
                                <button class="w-75 p-2 fw-bold fs-5">Dosen</button>
                            </div>
                            <div>
                                <button class="w-75 p-2 fw-bold fs-5">Admin</button>
                            </div>
                            <div>
                                <button class="w-75 p-2 fw-bold fs-5">Mahasiswa</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


</html>