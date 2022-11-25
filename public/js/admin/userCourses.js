


let app = new Vue({
    el: '#app',
    data: {
        categoryList: categories,
        categoryCourseObj : categoryCourses,
        categoryCheckList: categoryChecks,
        optionString: null
    },
    methods: {
        changeOption(category_id) {
            let courses = this.categoryCourseObj[category_id];
            let flag = false;
            if(this.categoryCheckList[category_id]) {
                flag = true;
            }
            changeAllCheck(courses , flag);
        },
        toSubmit() {
            var keys = Object.keys(this.categoryCourseObj);//category_id
            var options = {};
            for(let i=0;i<keys.length;i++) {
                let category_id = keys[i];
                let tmp = [];
                for(let j=0;j< this.categoryCourseObj[category_id].length;j++) {
                    let course = this.categoryCourseObj[category_id][j];
                    if(course.check===true) {
                        tmp.push(course.id);
                    }
                }
                if(tmp.length>0) {
                    options[category_id] = JSON.parse(JSON.stringify(tmp));
                }
            }
            this.optionString = JSON.stringify(options);
            let el = document.getElementById('updateUserCourses');
            app.$nextTick(() => {
                el.submit();
                $.LoadingOverlay("show");
            });
        }
    }
});

function changeAllCheck(list , flag) {
    for(let i=0;i<list.length;i++) {
        let tmp = list[i];
        tmp.check = flag;
    }
}

