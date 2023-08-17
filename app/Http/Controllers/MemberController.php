<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $member = Member::orderBy('name', 'asc')->get();
        return response()->json([
            'member' => $member
        ], 200);
    }


    public function store(Request $request)
    {
        // Validation
        $attr = $request->validate([
            'name'      => 'required|string|unique:members,name',
            'address'   => 'required'
        ]);

        // Split the full name into an array of words
        $nameArray = explode(' ', $attr['name']);

        // Get the first element of the array (the first name)
        $firstName = $nameArray[0];

        // Convert the first name to uppercase using mb_strtoupper()
        $uppercaseFirstName = mb_strtoupper($firstName);

        // Generate a random 4-digit number
        $randomNumber = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Create the code_member by combining the uppercase first name and random number
        $codeMember = $uppercaseFirstName . $randomNumber;

        $member = Member::create([
            'code_member' => $codeMember,
            'name'        => $attr['name'],
            'address'     => $attr['address'],
        ]);

        return response()->json([
            'member'  => $member,
            'message' => 'Member berhasil dibuat',
        ], 200);
    }


    public function editmember(Request $request, $id)
    {
        // Validation
        $attr = $request->validate([
            'name'          => 'required|string',
            'address'       => 'required',
        ]);

        $member = Member::find($id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        // Update product data
        $member->name          = $attr['name'];
        $member->address       = $attr['address'];

        $member->save();

        return response()->json([
            'message' => 'Member berhasil diupdate',
        ], 200);
    }

    public function destroy($id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response([
                'message' => 'Member tidak ditemukan.'
            ], 403);
        }

        $member->delete();

        return response()->json([
            'message' => 'Member berhasil dihapus.'
        ]);
    }
}
