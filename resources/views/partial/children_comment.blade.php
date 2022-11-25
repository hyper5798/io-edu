<div>
    <div>
        <span class="text-primary font-weight-bold">@{{ child.user_name }}</span>
        <span>@{{ child.created_at }}</span>
    </div>
    <div class="mb-2">
        @{{ child.comment }}
    </div>
    <div>

        <span v-if="isComment" class="replyButton" @click="toShowChildReply(item.id, inx, child.user_name)">回覆</span>


    </div>
</div>

<!-- Reply comment -->

<div v-show="commentChildren[item.id][inx].reply_show && isComment" >
    <div class="replyBlock">
        <div>
            @{{ data.user.name }}
        </div>
        <div class="commentTextBlock">
            <textarea id="story" name="story" v-model="child.reply"
                      rows="1" placeholder="回覆" class="commentTextarea">
            </textarea>

            <button type="button " class="btn- btn-outline-primary commentButton" @Click="toSendChildReply(item.id, inx, child.reply, child.user_id)">發佈</button>

        </div>

    </div>
</div>
