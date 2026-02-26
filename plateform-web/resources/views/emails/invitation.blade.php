<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invitation à rejoindre une colocation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>Invitation à rejoindre une colocation</h2>
        
        <p>Bonjour,</p>
        
        <p>Vous avez été invité(e) à rejoindre la colocation <strong>{{ $invitation->colocation->name }}</strong>.</p>
        
        @if($invitation->colocation->description)
            <p><em>{{ $invitation->colocation->description }}</em></p>
        @endif
        
        <p>Pour accepter cette invitation, cliquez sur le lien ci-dessous :</p>
        
        <p style="margin: 30px 0;">
            <a href="{{ route('invitations.show', $invitation->token) }}" 
               style="background-color: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                Voir l'invitation
            </a>
        </p>
        
        <p style="color: #666; font-size: 14px;">
            Si vous n'avez pas demandé cette invitation, vous pouvez ignorer cet email.
        </p>
    </div>
</body>
</html>
