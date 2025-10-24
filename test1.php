<?php
// --- PHP logic section (runs before HTML renders) ---
$greeting = "Hello Tailwind + PHP 🎉";
$dateText = "This is dynamic PHP text — " . date('l, F j, Y');
$buttonLabel = "Click Me";
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tailwind PHP Test</title>

  <!-- ✅ Tailwind via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Optional: Custom Tailwind config -->
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: '#3b82f6',
          },
        },
      },
    }
  </script>
</head>
<body class="bg-gray-900 text-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-gray-800 p-8 rounded-2xl shadow-2xl w-[400px] text-center">
    <h1 class="text-3xl font-bold text-brand mb-2">
      <?php echo $greeting; ?>
    </h1>

    <p class="text-gray-400 mb-4">
      <?php echo $dateText; ?>
    </p>

    <button class="bg-brand hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
      <?php echo $buttonLabel; ?>
    </button>
  </div>

</body>
</html>
