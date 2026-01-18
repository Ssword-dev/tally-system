<?php

namespace Tests\Unit;

use App\Models\BaseModel;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use App\Models\ClassModel;
use App\Models\ActivityModel;
use App\Models\ScoreModel;

describe('Models', function () {
    it('StudentModel can be instantiated', function () {
        $model = new StudentModel();
        expect($model)->toBeInstanceOf(StudentModel::class);
    });

    it('TeacherModel can be instantiated', function () {
        $model = new TeacherModel();
        expect($model)->toBeInstanceOf(TeacherModel::class);
    });

    it('ClassModel can be instantiated', function () {
        $model = new ClassModel();
        expect($model)->toBeInstanceOf(ClassModel::class);
    });

    it('ActivityModel can be instantiated', function () {
        $model = new ActivityModel();
        expect($model)->toBeInstanceOf(ActivityModel::class);
    });

    it('ScoreModel can be instantiated', function () {
        $model = new ScoreModel();
        expect($model)->toBeInstanceOf(ScoreModel::class);
    });

    it('BaseModel exists and can be extended', function () {
        expect(class_exists(BaseModel::class))->toBeTrue();
    });
});
