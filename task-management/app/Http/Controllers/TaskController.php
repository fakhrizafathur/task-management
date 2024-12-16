<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    // Mengambil semua tugas
    public function index(Request $request)
    {
        // Mendapatkan pengguna yang terautentikasi
        $user = JWTAuth::user();
        
        // Mengambil parameter status dari query string
        $status = $request->query('status');
        
        // Memeriksa role user
        if ($user->role === 'Admin') {
            // Jika user adalah Admin, tampilkan semua tugas
            $tasksQuery = Task::query();
        } else {
            // Jika user bukan Admin, tampilkan hanya tugas milik user
            $tasksQuery = Task::where('user_id', $user->id);
        }

        // Jika ada parameter status, filter berdasarkan status
        if ($status) {
            $tasksQuery->where('status', $status);
        }

        // Ambil hasil query dan kembalikan sebagai respons JSON
        $tasks = $tasksQuery->get();
        
        return response()->json($tasks);
    }

    // Menampilkan tugas berdasarkan ID
    public function show($id)
    {
        // Mendapatkan tugas berdasarkan ID
        $task = Task::find($id);

        // Jika tugas tidak ditemukan
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Mengembalikan tugas sebagai JSON
        return response()->json($task);
    }

    // Menambahkan tugas baru
    public function store(Request $request)
    {
        // Validasi input dari request
        $validated = $request->validate([
            'title' => 'required|string|max:255',  // Validasi untuk title, max 255 karakter
            'description' => 'required|string',   // Validasi untuk description, boleh panjang, sesuai dengan TEXT di database
            'status' => 'required|in:Selesai,Belum Selesai',  // Validasi untuk status, hanya bisa memilih antara 'Selesai' dan 'Belum Selesai'
        ]);

        // Menyimpan data task ke dalam database
        $task = new Task();
        $task->title = $validated['title'];
        $task->description = $validated['description'];
        $task->status = $validated['status'];
        $task->user_id = auth()->user()->id; // Menyimpan user_id dari JWT yang terautentikasi
        $task->save();

        return response()->json($task, 201);  // Mengembalikan response dengan data task yang baru ditambahkan
    }

    // Memperbarui tugas
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Belum Selesai,Selesai' // Validasi input status
        ]);

        $task = Task::findOrFail($id); // Cari tugas berdasarkan ID
        $task->status = $request->input('status'); // Perbarui status
        $task->save(); // Simpan ke database

        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
    }

    // Menghapus tugas
    public function destroy($id)
    {
        // Cek apakah task ada
        $task = Task::find($id);

        // Jika task tidak ditemukan, kembalikan error 404
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        // Cek apakah task milik user yang sedang login
        if ($task->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Hapus task
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
