{if $messages}
    <div class="message">
        {if is_numeric($message_type)}
            <a href="?page=messages&category={$message_type}">{$messages}</a>
        {else}
            <a href="?page=messages">{$messages}</a>
        {/if}
    </div>
{/if}