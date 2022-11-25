<div>
    <div>
        @{{ data.user.name }}
    </div>
    <div class="commentTextBlock">
        <textarea id="story" name="story" v-model="comment"
                  rows="1" placeholder="回覆" class="commentTextarea">
        </textarea>

        <button type="button" class="btn- btn-outline-dark commentButton" @Click="toSendComment()">發佈</button>

    </div>

</div>
