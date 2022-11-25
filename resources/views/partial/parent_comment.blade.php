<div>
    <div>
        <span class="text-primary font-weight-bold">@{{ item.user_name }}</span>
        <span>@{{ item.created_at }}</span>
    </div>
    <div class="mb-2">
        @{{ item.comment }}
    </div>
    <div>
        <span v-if="courseCheck" class="replyButton" @click="switchParentReply(index)">回覆</span>
        <span class="replyButton" @click="switchChildReply(index)">回覆數 ( @{{ commentChildren[item.id].length }} )</span>
    </div>
</div>

<!-- Reply comment -->
<div v-show="commentReplyObj[index]['replyComment'] && isComment">
    <div class="replyBlock">
        <div>
            @{{ data.user.name }}
        </div>
        <div class="commentTextBlock">
            <textarea id="story" name="story" v-model="commentReplyObj[index]['comment']"
                      rows="1" placeholder="回覆" class="commentTextarea">
            </textarea>

            <button type="button" class="btn- btn-outline-primary commentButton" @Click="toSendReply(index, item.id, commentReplyObj[index]['comment'])">發佈</button>

        </div>

    </div>
</div>
