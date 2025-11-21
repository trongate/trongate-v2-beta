<div style="max-width: 600px;">
    <h1>Hello from the Welcome Module!</h1>
    
    <p>This is the <strong>hello.php</strong> view file from the welcome module.</p>
    
    <p>It's being rendered inside the <strong>admin template</strong> which comes from the templates module.</p>
    
    <div style="background: #f0f0f0; padding: 1rem; border-left: 4px solid #0066cc; margin: 1rem 0;">
        <h3>How This Works:</h3>
        <ol>
            <li>You visited: <code><?= current_url() ?></code></li>
            <li>Welcome controller called: <code>$this->templates->admin($data)</code></li>
            <li>Templates module rendered: <code>modules/templates/views/admin.php</code></li>
            <li>Admin template included: <code>modules/welcome/views/hello.php</code> (this file!)</li>
        </ol>
    </div>
    
    <p><strong>Congratulations!</strong> The new templates system is working. 🎉</p>
</div>