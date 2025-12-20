<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends ResponseController
{
    /**
     * Login user with email or phone
     */
    public function login(Request $request)
    {
        // Allow login with either 'email' or 'phone'
        // If the client sends 'login' field, we determine if it's an email or phone
        $loginField = $request->input('login');
        if (!$loginField) {
             // Fallback to legacy behavior if they send 'email' or 'phone' explicitly
             $loginField = $request->input('email') ?? $request->input('phone');
        }

        if (!$loginField) {
            return $this->error('Email or Phone number is required', 400);
        }

        // Determine if input is email or phone
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $credentials = [
            $fieldType => $loginField,
            'password' => $request->input('password')
        ];

        $user = User::where($fieldType, $loginField)->first();

        if($user){
            if(Hash::check($credentials['password'], $user->password)){
                $token = $user->createToken('auth-token')->plainTextToken;
                
                return $this->success(
                    'Connexion réussie', 
                        [
                            'token' => $token, 
                            'user' => $user->load('wallet')
                        ], 201
                    );
            } else {
                return $this->error('Mot de passe incorrect', 401);
            }
        } else {
            return $this->error('Utilisateur non trouvé', 404);
        }
    }
    
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return $this->success('Déconnexion réussie');
    }

    public function register(Request $request){
        // Custom error messages in French
        $messages = [
            'firstname.required' => 'Le prénom est obligatoire',
            'phone.required' => 'Le numéro de téléphone est obligatoire',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé',
            'email.email' => 'L\'adresse email doit être valide',
            'email.unique' => 'Cette adresse email est déjà utilisée',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'id_card_recto.required' => 'La photo recto de la pièce d\'identité est obligatoire',
            'id_card_recto.image' => 'Le fichier recto doit être une image',
            'id_card_verso.required' => 'La photo verso de la pièce d\'identité est obligatoire',
            'id_card_verso.image' => 'Le fichier verso doit être une image',
            'role.in' => 'Le rôle sélectionné n\'est pas valide',
        ];

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sex' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255|unique:users,phone',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'role' => 'nullable|string|in:acheteur,vendeur',
            'id_card_recto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_card_verso' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);

        if ($validator->fails()) {
            return $this->error('Erreur de validation', $validator->errors(), 422);
        }

        $inputs = $validator->validated();

        if($request->hasFile('avatar')){
            $avatar = $request->file('avatar');
            $avatarName = time() . '_avatar.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('avatars'), $avatarName);
            $inputs['avatar'] = $avatarName;
        }

        if($request->hasFile('id_card_recto')){
            $recto = $request->file('id_card_recto');
            $rectoName = time() . '_recto.' . $recto->getClientOriginalExtension();
            $recto->move(public_path('id_cards'), $rectoName);
            $inputs['id_card_recto'] = $rectoName;
        }

        if($request->hasFile('id_card_verso')){
            $verso = $request->file('id_card_verso');
            $versoName = time() . '_verso.' . $verso->getClientOriginalExtension();
            $verso->move(public_path('id_cards'), $versoName);
            $inputs['id_card_verso'] = $versoName;
        }

        $user = User::create([
            'firstname' => $inputs['firstname'],
            'lastname' => $inputs['lastname'] ?? null,
            'avatar' => $inputs['avatar'] ?? null,
            'sex' => $inputs['sex'] ?? null,
            'address' => $inputs['address'] ?? null,
            'phone' => $inputs['phone'],
            'email' => $inputs['email'] ?? null,
            'password' => Hash::make($inputs['password']),
            'city' => $inputs['city'] ?? null,
            'country' => $inputs['country'] ?? null,
            'role' => $inputs['role'] ?? 'acheteur',
            'id_card_recto' => $inputs['id_card_recto'], // Required now
            'id_card_verso' => $inputs['id_card_verso'], // Required now
        ]);

        // Create wallet for the user
        $user->wallet()->create([
            'balance' => 0
        ]);
        $token = $user->createToken('auth-token')->plainTextToken;
        return $this->success(
            'Inscription réussie', 
                [
                    'token' => $token, 
                    'user' => $user->load('wallet')
                ], 201
            );
    }
}
